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
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px" onclick="addModal()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Event
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
				<!-- <div class="box-header">
					<h3 class="box-title">Serial Number Report Filters</h3>
				</div> -->
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableXibo" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 2%">Code</th>
										<th style="width: 2%">Category</th>
										<th style="width: 5%">Title</th>
										<!-- <th style="width: 3%">Title JP</th>
										<th style="width: 3%">Title Color</th>
										<th style="width: 3%">Sub Title</th>
										<th style="width: 3%">Sub Title JP</th>
										<th style="width: 3%">Sub Title Color</th>
										<th style="width: 3%">Content</th>
										<th style="width: 3%">Content JP</th>
										<th style="width: 3%">Content Font Size</th> -->
										<th style="width: 2%">Status</th>
										<th style="width: 3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableXibo">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Event</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="code" id="code">
								<input type="hidden" name="edit_count" id="edit_count">
								<input type="hidden" name="edit_count_content" id="edit_count_content">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Category</label>
									<div class="col-sm-5" align="left" id="divEditCategory">
										<select style="width: 100%" class="form-control" id="edit_category" data-placeholder="Pilih Category" align="left">
											<option value=""></option>
											<option value="template-1">SHARING PASSTION & PERFORMANCE</option>
											<option value="template-2">Tamu Indonesia</option>
											<option value="template-3">Tamu Jepang</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Title</label>
									<div class="col-sm-5" align="left">
										<input type="text" name="edit_title" id="edit_title" class="form-control" placeholder="Input Title">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4 text-blue">Title JP</label>
									<div class="col-sm-5" align="left">
										<input type="text" name="edit_title_jp" id="edit_title_jp" class="form-control" placeholder="Input Title JP">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Title Color</label>
									<div class="col-sm-5" align="left">
										<input type="color" name="edit_color_title" id="edit_color_title" class="form-control" placeholder="Input Title Color">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Status</label>
									<div class="col-sm-5" align="left" id="divEditStatus">
										<select style="width: 100%" class="form-control" id="edit_status" data-placeholder="Pilih Status" align="left">
											<option value=""></option>
											<option value="0">Non-Active</option>
											<option value="1">Active</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Add Sub Title</label>
									<div class="col-sm-5" align="left">
										<button class="btn btn-success" onclick="addSubTitle()"><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="box-body" id="divEditSubTitle">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="add-modal" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Event</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="add_count" id="add_count" value="1">
								<input type="hidden" name="add_count_content" id="add_count_content" value="1">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Category</label>
									<div class="col-sm-5" align="left" id="divAddCategory">
										<select style="width: 100%" class="form-control" id="add_category" data-placeholder="Pilih Category" align="left">
											<option value=""></option>
											<option value="template-1">SHARING PASSTION & PERFORMANCE</option>
											<option value="template-2">Tamu Indonesia</option>
											<option value="template-3">Tamu Jepang</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Title</label>
									<div class="col-sm-5" align="left">
										<input type="text" name="add_title" id="add_title" class="form-control" placeholder="Input Title">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4 text-blue">Title JP</label>
									<div class="col-sm-5" align="left">
										<input type="text" name="add_title_jp" id="add_title_jp" class="form-control" placeholder="Input Title JP">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Title Color</label>
									<div class="col-sm-5" align="left">
										<input type="color" name="add_color_title" id="add_color_title" class="form-control" placeholder="Input Title Color">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Status</label>
									<div class="col-sm-5" align="left" id="divAddStatus">
										<select style="width: 100%" class="form-control" id="add_status" data-placeholder="Pilih Status" align="left">
											<option value=""></option>
											<option value="0">Non-Active</option>
											<option value="1">Active</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Add Sub Title</label>
									<div class="col-sm-5" align="left">
										<button class="btn btn-success" onclick="addSubTitleNew()"><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="box-body" id="divAddSubTitle">
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

	var xibo_all = null;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		$('#edit_category').select2({
			allowClear:true,
			dropdownParent: $('#divEditCategory'),
		});

		$('#edit_status').select2({
			allowClear:true,
			dropdownParent: $('#divEditStatus'),
		});

		$('#add_category').select2({
			allowClear:true,
			dropdownParent: $('#divAddCategory'),
		});
		
		$('#add_status').select2({
			allowClear:true,
			dropdownParent: $('#divAddStatus'),
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	var availability = ['Non-Active','Active'];
	var cat = ['SHARING PASSION AND PERFORMANCE','Tamu Indonesia','Tamu Jepang'];

	function fillData(){
		$('#loading').show();
		
		$.get('{{ url("fetch/general/xibo") }}', function(result, status, xhr){
			if(result.status){
				if (result.xibo != null) {
					$('#tableXibo').DataTable().clear();
					$('#tableXibo').DataTable().destroy();
					$('#bodyTableXibo').html("");
					var tableXibo = "";
					
					var index = 1;

					$.each(result.xibo, function(key, value) {
						tableXibo += '<tr>';
						tableXibo += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableXibo += '<td style="text-align:left;padding-left:7px;">'+value.code+'</td>';
						tableXibo += '<td style="text-align:left;padding-left:7px;">'+cat[value.category.split('-')[1]-1]+'</td>';
						tableXibo += '<td style="text-align:left;padding-left:7px;">'+value.title+'</td>';
						tableXibo += '<td style="text-align:left;padding-left:7px;">'+availability[value.status]+'</td>';
						var url = '{{url("index/general/xibo/display")}}/'+value.code;
						tableXibo += '<td style="text-align:center"><button class="btn btn-warning btn-sm" onclick="editXibo(\''+value.code+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteXibo(\''+value.code+'\')"><i class="fa fa-trash"></i></button><a style="margin-left:7px;" target="_blank" class="btn btn-info btn-sm" href="'+url+'"><i class="fa fa-television"></i></a></td>';
						tableXibo += '</tr>';
						index++;
					});
					$('#bodyTableXibo').append(tableXibo);

					var table = $('#tableXibo').DataTable({
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

				xibo_all = result.xibo_all;

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function editXibo(code) {
		$('#divEditSubTitle').html('');
		$("#code").val(code);
		var div_sub_title = '';
		var index = 1;
		var index_content = 1;
		for(var i = 0; i < xibo_all.length;i++){
			if (xibo_all[i].code == code) {
				$('#edit_category').val(xibo_all[i].category).trigger('change');
				$('#edit_status').val(xibo_all[i].status).trigger('change');
				$('#edit_title').val(xibo_all[i].title);
				$('#edit_title_jp').val(xibo_all[i].title_jp);
				$('#edit_color_title').val(xibo_all[i].color_title);

				div_sub_title += '<div class="col-xs-12" style="border: 1px solid black;margin-top:10px;padding:0px;" id="div_sub_title_'+index+'">';

				div_sub_title += '<center class="col-xs-11" style="background-color:orange;color:white;padding:7px;margin-bottom:10px;">';
				div_sub_title += '<span style="font-weight:bold">Sub Title '+index;
				div_sub_title += '</span>';
				div_sub_title += '</center>';

				div_sub_title += '<div class="col-xs-1" style="padding-left:0px;">';
				var idsub = 'div_sub_title_'+index;
				div_sub_title += '<button style="width:100%;margin-left:10px;" onclick="deleteSubTitle(\''+idsub+'\')" class="btn btn-danger"><i class="fa fa-minus"></i></button>';
				div_sub_title += '</div>';

				div_sub_title += '<div class="form-group row" align="right">';
				div_sub_title += '<label class="col-sm-3">Sub Title '+(index)+'</label>';
				div_sub_title += '<div class="col-sm-7" align="left">';
				div_sub_title += '<input type="text" name="edit_sub_title_'+(index)+'" id="edit_sub_title_'+(index)+'" class="form-control" value="'+(xibo_all[i].sub_title || '')+'" placeholder="Input Sub Title">';
				div_sub_title += '</div>';
				div_sub_title += '<div class="col-sm-2" align="left">';
				div_sub_title += '<button class="btn btn-success" onclick="add_new_content(\''+index+'\')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Content</button>';
				div_sub_title += '</div>';
				div_sub_title += '</div>';

				div_sub_title += '<div class="form-group row" align="right">';
				div_sub_title += '<label class="col-sm-3 text-blue">Sub Title JP '+(index)+'</label>';
				div_sub_title += '<div class="col-sm-7" align="left">';
				div_sub_title += '<input type="text" name="edit_sub_title_jp_'+(index)+'" id="edit_sub_title_jp_'+(index)+'" class="form-control" value="'+(xibo_all[i].sub_title_jp || '')+'" placeholder="Input Sub Title JP">';
				div_sub_title += '</div>';
				div_sub_title += '</div>';

				div_sub_title += '<div class="form-group row" align="right">';
				div_sub_title += '<label class="col-sm-3">Sub Title Color '+(index)+'</label>';
				div_sub_title += '<div class="col-sm-7" align="left">';
				div_sub_title += '<input type="color" name="edit_color_sub_title_'+(index)+'" id="edit_color_sub_title_'+(index)+'" class="form-control" value="'+xibo_all[i].color_sub_title+'">';
				div_sub_title += '</div>';
				div_sub_title += '</div>';

				if (xibo_all[i].content != null && xibo_all[i].content.match(/_/gi)) {
					if (xibo_all[i].content != null) {
						var contents = xibo_all[i].content.split('_');
					}else{
						var contents = null;
					}
					if (xibo_all[i].content_jp != null) {
						var contents_jp = xibo_all[i].content_jp.split('_');
					}else{
						var contents_jp = null;
					}
					for(var j = 0; j < contents.length;j++){
						div_sub_title += '<div id="div_edit_content_'+index+'_'+index_content+'">';
						if (contents != null) {
							div_sub_title += '<div class="form-group row" align="right">';
							div_sub_title += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
							div_sub_title += '<div class="col-sm-7" align="left">';
							div_sub_title += '<input type="text" name="edit_content_'+(index)+'_'+index_content+'" id="edit_content_'+(index)+'_'+index_content+'" class="form-control" value="'+(contents[j] || '')+'" placeholder="Input Content">';
							div_sub_title += '</div>';
							if (j != 0) {
								div_sub_title += '<div class="col-sm-2" align="left" style="padding-left:0px;">';
								div_sub_title += '<button class="btn btn-danger" onclick="delete_new_content(\''+index+'\',\''+index_content+'\')"><i class="fa fa-minus"></i></button>';
								div_sub_title += '</div>';
							}
							div_sub_title += '</div>';
						}else{
							div_sub_title += '<div class="form-group row" align="right">';
							div_sub_title += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
							div_sub_title += '<div class="col-sm-7" align="left">';
							div_sub_title += '<input type="text" name="edit_content_'+(index)+'_'+index_content+'" id="edit_content_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content">';
							div_sub_title += '</div>';
							if (j != 0) {
								div_sub_title += '<div class="col-sm-2" align="left" style="padding-left:0px;">';
								div_sub_title += '<button class="btn btn-danger" onclick="delete_new_content(\''+index+'\',\''+index_content+'\')"><i class="fa fa-minus"></i></button>';
								div_sub_title += '</div>';
							}
							div_sub_title += '</div>';
						}

						if (contents_jp != null) {
							div_sub_title += '<div class="form-group row" align="right">';
							div_sub_title += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
							div_sub_title += '<div class="col-sm-7" align="left">';
							div_sub_title += '<input type="text" name="edit_content_jp_'+(index)+'_'+index_content+'" id="edit_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="'+(contents_jp[j] || '')+'" placeholder="Input Content JP">';
							div_sub_title += '</div>';
							div_sub_title += '</div>';
						}else{
							div_sub_title += '<div class="form-group row" align="right">';
							div_sub_title += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
							div_sub_title += '<div class="col-sm-7" align="left">';
							div_sub_title += '<input type="text" name="edit_content_jp_'+(index)+'_'+index_content+'" id="edit_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content JP">';
							div_sub_title += '</div>';
							div_sub_title += '</div>';
						}
						div_sub_title += '</div>';
						index_content++;
					}
				}else{
					div_sub_title += '<div id="div_edit_content_'+index+'_'+index_content+'">';
					div_sub_title += '<div class="form-group row" align="right">';
					div_sub_title += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
					div_sub_title += '<div class="col-sm-7" align="left">';
					div_sub_title += '<input type="text" name="edit_content_'+(index)+'_'+index_content+'" id="edit_content_'+(index)+'_'+index_content+'" class="form-control" value="'+(xibo_all[i].content || '')+'" placeholder="Input Content">';
					div_sub_title += '</div>';
					if (xibo_all[i].content != null && xibo_all[i].content.split('_').length != 1) {
						div_sub_title += '<div class="col-sm-2" align="left" style="padding-left:0px;">';
						div_sub_title += '<button class="btn btn-danger" onclick="delete_new_content(\''+index+'\',\''+index_content+'\')"><i class="fa fa-minus"></i></button>';
						div_sub_title += '</div>';
					}
					div_sub_title += '</div>';

					div_sub_title += '<div class="form-group row" align="right">';
					div_sub_title += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
					div_sub_title += '<div class="col-sm-7" align="left">';
					div_sub_title += '<input type="text" name="edit_content_jp_'+(index)+'_'+index_content+'" id="edit_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="'+(xibo_all[i].content_jp || '')+'" placeholder="Input Content JP">';
					div_sub_title += '</div>';
					div_sub_title += '</div>';
					div_sub_title += '</div>';
					index_content++;
				}

				div_sub_title += '<div id="div_new_content_'+index+'">';
				div_sub_title += '</div>';

				div_sub_title += '<div class="form-group row" align="right">';
				div_sub_title += '<label class="col-sm-3">Content Color '+(index)+'</label>';
				div_sub_title += '<div class="col-sm-7" align="left">';
				div_sub_title += '<input type="color" name="edit_color_content_'+(index)+'" id="edit_color_content_'+(index)+'" class="form-control" value="'+xibo_all[i].color_content+'" placeholder="Input Color Content">';
				div_sub_title += '</div>';
				div_sub_title += '</div>';

				div_sub_title += '<div class="form-group row" align="right">';
				div_sub_title += '<label class="col-sm-3">Content Font Size '+(index)+' (Pixels)</label>';
				div_sub_title += '<div class="col-sm-4" align="left">';
				div_sub_title += '<input type="text" name="edit_content_font_size_'+(index)+'" id="edit_content_font_size_'+(index)+'" class="form-control" value="'+(xibo_all[i].content_font_size || '')+'" placeholder="Input Content Font Size">';
				div_sub_title += '</div>';
				div_sub_title += '</div>';

				div_sub_title += '</div>';

				index++;
			}
		}
		$('#edit_count').val(index);
		$('#edit_count_content').val(index_content);
		$('#divEditSubTitle').append(div_sub_title);
		$('#edit-modal').modal('show');
	}

	function addSubTitle() {
		var div_sub_title = '';

		var index = $('#edit_count').val();
		var index_content = $('#edit_count_content').val();

		div_sub_title += '<div class="col-xs-12" style="border: 1px solid black;margin-top:10px;padding:0px;" id="div_sub_title_'+index+'">';

		div_sub_title += '<center class="col-xs-11" style="background-color:orange;color:white;padding:7px;margin-bottom:10px;">';
		div_sub_title += '<span style="font-weight:bold">Sub Title '+index;
		div_sub_title += '</span>';
		div_sub_title += '</center>';

		div_sub_title += '<div class="col-xs-1" style="padding-left:0px;">';
		var idsub = 'div_sub_title_'+index;
		div_sub_title += '<button style="width:100%;margin-left:10px;" onclick="deleteSubTitle(\''+idsub+'\')" class="btn btn-danger"><i class="fa fa-minus"></i></button>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Sub Title '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="edit_sub_title_'+(index)+'" id="edit_sub_title_'+(index)+'" class="form-control" value="" placeholder="Input Sub Title">';
		div_sub_title += '</div>';
		div_sub_title += '<div class="col-sm-2" align="left">';
		div_sub_title += '<button class="btn btn-success" onclick="add_new_content(\''+index+'\')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Content</button>';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3 text-blue">Sub Title JP '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="edit_sub_title_jp_'+(index)+'" id="edit_sub_title_jp_'+(index)+'" class="form-control" value="" placeholder="Input Sub Title JP">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Sub Title Color '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="color" name="edit_color_sub_title_'+(index)+'" id="edit_color_sub_title_'+(index)+'" class="form-control" value="">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div id="div_edit_content_'+index+'_'+index_content+'">';
		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="edit_content_'+(index)+'_'+index_content+'" id="edit_content_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';
	
		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="edit_content_jp_'+(index)+'_'+index_content+'" id="edit_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content JP">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div id="div_new_content_'+index+'">';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content Color '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="color" name="edit_color_content_'+(index)+'" id="edit_color_content_'+(index)+'" class="form-control" value="" placeholder="Input Color Content">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content Font Size '+(index)+' (Pixels)</label>';
		div_sub_title += '<div class="col-sm-4" align="left">';
		div_sub_title += '<input type="text" name="edit_content_font_size_'+(index)+'" id="edit_content_font_size_'+(index)+'" class="form-control" value="" placeholder="Input Content Font Size">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '</div>';

		$('#edit_count').val(parseInt(index)+1);
		$('#edit_count_content').val(parseInt(index_content)+1);

		$('#divEditSubTitle').append(div_sub_title);
	}

	function delete_new_content(index,index_content) {
		$("#div_edit_content_"+index+'_'+index_content).remove();
	}

	function add_new_content(index) {
		var div_content = '';
		var index_content = $('#edit_count_content').val();

		div_content += '<div id="div_edit_content_'+index+'_'+index_content+'">';
		div_content += '<div class="form-group row" align="right">';
		div_content += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
		div_content += '<div class="col-sm-7" align="left">';
		div_content += '<input type="text" name="edit_content_'+(index)+'_'+index_content+'" id="edit_content_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content">';
		div_content += '</div>';
		div_content += '<div class="col-sm-2" align="left" style="padding-left:0px;">';
		div_content += '<button class="btn btn-danger" onclick="delete_new_content(\''+index+'\',\''+index_content+'\')"><i class="fa fa-minus"></i></button>';
		div_content += '</div>';
		div_content += '</div>';
	
		div_content += '<div class="form-group row" align="right">';
		div_content += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
		div_content += '<div class="col-sm-7" align="left">';
		div_content += '<input type="text" name="edit_content_jp_'+(index)+'_'+index_content+'" id="edit_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content JP">';
		div_content += '</div>';
		div_content += '</div>';
		div_content += '</div>';

		$('#div_new_content_'+index).append(div_content);

		$('#edit_count_content').val(parseInt(index_content)+1);
	}

	function addModal() {
		$('#divAddSubTitle').html('');
		$('#add_category').val('').trigger('change');
		$('#add_title').val('');
		$('#add_title_jp').val('');
		$('#add_status').val('').trigger('change');
		$('#add_count').val(1);
		$('#add_count_content').val(1);
	}

	function deleteSubTitle(id) {
		$('#'+id).remove();
	}

	function update() {
		if (confirm('Apakah Anda Yakin?')) {
			$('#loading').show();
			var code = $('#code').val();
			var category = $('#edit_category').val();
			var status = $('#edit_status').val();
			var title = $('#edit_title').val();
			var title_jp = $('#edit_title_jp').val();
			var color_title = $('#edit_color_title').val();		

			var edit_count = $('#edit_count').val();
			var edit_count_content = $('#edit_count_content').val();

			var sub_title = [];
			var sub_title_jp = [];
			var color_sub_title = [];

			var content = [];
			var content_jp = [];
			var color_content = [];
			var content_font_size = [];

			for(var i = 1; i < parseInt(edit_count);i++){
				if ($('#div_sub_title_'+i).text() != '') {
					sub_title.push($('#edit_sub_title_'+i).val());
					sub_title_jp.push($('#edit_sub_title_jp_'+i).val());
					color_sub_title.push($('#edit_color_sub_title_'+i).val());

					var contents = [];
					var content_jps = [];

					for(var j = 1; j < parseInt(edit_count_content);j++){
						if ($('#div_edit_content_'+i+'_'+j).text() != '') {
							contents.push($('#edit_content_'+i+'_'+j).val());
							content_jps.push($('#edit_content_jp_'+i+'_'+j).val());
						}
					}
					color_content.push($('#edit_color_content_'+i).val());
					content_font_size.push($('#edit_content_font_size_'+i).val());

					content.push(contents.join('_'));
					content_jp.push(content_jps.join('_'));
				}
			}

			var data = {
				code:code,
				category:category,
				status:status,
				title:title,
				title_jp:title_jp,
				color_title:color_title,
				edit_count:edit_count,
				edit_count_content:edit_count_content,
				sub_title:sub_title,
				sub_title_jp:sub_title_jp,
				color_sub_title:color_sub_title,
				content:content,
				content_jp:content_jp,
				color_content:color_content,
				content_font_size:content_font_size,
			}

			$.post('{{ url("update/general/xibo") }}',data, function(result, status, xhr){
				if(result.status){
					$('#edit-modal').modal('hide');
					fillData();
					openSuccessGritter('Success!','Sukses Update Data');
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
					return false;
				}
			})	
		}
	}

	function addSubTitleNew() {
		var div_sub_title = '';

		var index = $('#add_count').val();
		var index_content = $('#add_count_content').val();

		div_sub_title += '<div class="col-xs-12" style="border: 1px solid black;margin-top:10px;padding:0px;" id="div_sub_title_'+index+'">';

		div_sub_title += '<center class="col-xs-11" style="background-color:orange;color:white;padding:7px;margin-bottom:10px;">';
		div_sub_title += '<span style="font-weight:bold">Sub Title '+index;
		div_sub_title += '</span>';
		div_sub_title += '</center>';

		div_sub_title += '<div class="col-xs-1" style="padding-left:0px;">';
		var idsub = 'div_sub_title_'+index;
		div_sub_title += '<button style="width:100%;margin-left:10px;" onclick="deleteSubTitle(\''+idsub+'\')" class="btn btn-danger"><i class="fa fa-minus"></i></button>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Sub Title '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="add_sub_title_'+(index)+'" id="add_sub_title_'+(index)+'" class="form-control" value="" placeholder="Input Sub Title">';
		div_sub_title += '</div>';
		div_sub_title += '<div class="col-sm-2" align="left">';
		div_sub_title += '<button class="btn btn-success" onclick="add_new_content(\''+index+'\')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Content</button>';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3 text-blue">Sub Title JP '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="add_sub_title_jp_'+(index)+'" id="add_sub_title_jp_'+(index)+'" class="form-control" value="" placeholder="Input Sub Title JP">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Sub Title Color '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="color" name="add_color_sub_title_'+(index)+'" id="add_color_sub_title_'+(index)+'" class="form-control" value="">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div id="div_add_content_'+index+'_'+index_content+'">';
		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content '+(index)+'-'+(index_content)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="add_content_'+(index)+'_'+index_content+'" id="add_content_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';
	
		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3 text-blue">Content JP '+(index)+'-'+(index_content)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="text" name="add_content_jp_'+(index)+'_'+index_content+'" id="add_content_jp_'+(index)+'_'+index_content+'" class="form-control" value="" placeholder="Input Content JP">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div id="div_new_content_'+index+'">';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content Color '+(index)+'</label>';
		div_sub_title += '<div class="col-sm-7" align="left">';
		div_sub_title += '<input type="color" name="add_color_content_'+(index)+'" id="add_color_content_'+(index)+'" class="form-control" value="" placeholder="Input Color Content">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '<div class="form-group row" align="right">';
		div_sub_title += '<label class="col-sm-3">Content Font Size '+(index)+' (Pixels)</label>';
		div_sub_title += '<div class="col-sm-4" align="left">';
		div_sub_title += '<input type="text" name="add_content_font_size_'+(index)+'" id="add_content_font_size_'+(index)+'" class="form-control" value="" placeholder="Input Content Font Size">';
		div_sub_title += '</div>';
		div_sub_title += '</div>';

		div_sub_title += '</div>';

		$('#add_count').val(parseInt(index)+1);
		$('#add_count_content').val(parseInt(index_content)+1);

		$('#divAddSubTitle').append(div_sub_title);
	}

	function add() {
		if (confirm('Apakah Anda Yakin?')) {
			$('#loading').show();
			var category = $('#add_category').val();
			var status = $('#add_status').val();
			var title = $('#add_title').val();
			var title_jp = $('#add_title_jp').val();
			var color_title = $('#add_color_title').val();		

			var add_count = $('#add_count').val();
			var add_count_content = $('#add_count_content').val();

			var sub_title = [];
			var sub_title_jp = [];
			var color_sub_title = [];

			var content = [];
			var content_jp = [];
			var color_content = [];
			var content_font_size = [];

			for(var i = 1; i < parseInt(add_count);i++){
				if ($('#div_sub_title_'+i).text() != '') {
					sub_title.push($('#add_sub_title_'+i).val());
					sub_title_jp.push($('#add_sub_title_jp_'+i).val());
					color_sub_title.push($('#add_color_sub_title_'+i).val());

					var contents = [];
					var content_jps = [];

					for(var j = 1; j < parseInt(add_count_content);j++){
						if ($('#div_add_content_'+i+'_'+j).text() != '') {
							contents.push($('#add_content_'+i+'_'+j).val());
							content_jps.push($('#add_content_jp_'+i+'_'+j).val());
						}
					}
					color_content.push($('#add_color_content_'+i).val());
					content_font_size.push($('#add_content_font_size_'+i).val());

					content.push(contents.join('_'));
					content_jp.push(content_jps.join('_'));
				}
			}

			var data = {
				category:category,
				status:status,
				title:title,
				title_jp:title_jp,
				color_title:color_title,
				add_count:add_count,
				add_count_content:add_count_content,
				sub_title:sub_title,
				sub_title_jp:sub_title_jp,
				color_sub_title:color_sub_title,
				content:content,
				content_jp:content_jp,
				color_content:color_content,
				content_font_size:content_font_size,
			}

			$.post('{{ url("input/general/xibo") }}',data, function(result, status, xhr){
				if(result.status){
					$('#add-modal').modal('hide');
					fillData();
					openSuccessGritter('Success!','Sukses Add Data');
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
					return false;
				}
			})	
		}
	}

	function deleteXibo(code) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				code:code,
			}

			$.get('{{ url("delete/general/xibo") }}',data, function(result, status, xhr){
				if(result.status){
					fillData();
					openSuccessGritter('Success!','Sukses Delete Data');
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
					return false;
				}
			})
		}
	}

	const hexToDecimal = hex => parseInt(hex, 16);

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
</script>
@endsection