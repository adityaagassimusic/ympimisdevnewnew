@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 20px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal" onclick="cancelAll()" data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point Check</button>
		<a class="btn btn-primary btn-sm pull-right" href="#divSafety" style="margin-right: 5px"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Point Safety</a>
			
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
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
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div style="text-align: center;background-color: orange;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: white;font-size: 20px">
							POINT CHECK
						</span>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Dokumen</span>
							<div class="form-group">
								<select class="form-control select2" name="document_number" id="document_number" data-placeholder="Pilih Document" style="width: 100%;">
									<option></option>
									@foreach($process as $process)
									<option value="{{$process->document_number}}">{{$process->document_number}} - {{$process->document_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/special_process/') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/special_process/point_check/'.$category) }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tablePointCheck" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">No. Dokumen</th>
										<th width="2%">Nama Dokumen</th>
										<th width="3%">Process Pekerjaan</th>
										<th width="3%">Point Pekerjaan</th>
										<th width="3%">Safety Point</th>
										<th width="3%">Tipe Jawaban</th>
										<!-- <th width="3%">Alat Kelengkapan Diri</th>
										<th width="3%">Alat / Mesin yang Digunakan</th> -->
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTablePointCheck">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 5px;" id="divSafety">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<div style="text-align: center;background-color: green;margin-bottom: 20px">
								<span style="padding: 15px;font-weight: bold;color: white;font-size: 20px">
									KELENGKAPAN POINT SAFETY
								</span>
							</div>
							<table id="tablePointApd" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">No. Dokumen</th>
										<th width="2%">Nama Dokumen</th>
										<th width="1%">Category</th>
										<th width="3%">Alat Kelengkapan Diri</th>
										<th width="3%">Alat / Mesin yang Digunakan</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableApd">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-2">Document <span class="text-red">*</span></label>
									<div class="col-sm-10" align="left" id="divAddProcess">
										<select class="form-control" name="add_document" id="add_document" data-placeholder="Pilih Document" style="width: 100%;" onchange="checkDocument(this.value,this.id)">
											<option></option>
											@foreach($process2 as $process)
											<option value="{{$process->document_number}}_{{$process->document_name}}">{{$process->document_number}} - {{$process->document_name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Proses Pekerjaan</label>
									<div class="col-sm-10" align="left">
										<textarea id="add_work_process" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Point Pekerjaan</label>
									<div class="col-sm-10" align="left">
										<textarea id="add_work_point" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Safety Point</label>
									<div class="col-sm-10" align="left">
										<textarea id="add_work_safety" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Tipe Jawaban <span class="text-red">*</span></label>
									<div class="col-sm-10" align="left" id="divAddType">
										<select class="form-control" name="add_audit_type" id="add_audit_type" data-placeholder="Pilih Tipe Jawaban" style="width: 100%;" onchange="checkJawaban(this.value)">
											<option value="Normal">Normal</option>
											<option value="Isian">Isian</option>
										</select>
									</div>
								</div>
								<div id="div_add_value">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Batas Bawah</label>
										<div class="col-sm-10" align="left">
											<input type="text" id="add_lower" style="width: 100%" class="form-control numpad" placeholder="Isi Batas Bawah" readonly>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Batas Atas</label>
										<div class="col-sm-10" align="left">
											<input type="text" id="add_upper" style="width: 100%" class="form-control numpad" placeholder="Isi Batas Atas" readonly>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">UOM</label>
										<div class="col-sm-10" align="left" id="divAddUom">
											<select class="form-control" name="add_uom" id="add_uom" data-placeholder="Pilih UOM" style="width: 100%;">
											<option value=""></option>
											@foreach($uom as $uom)
											<option value="{{$uom}}">{{$uom}}</option>
											@endforeach
										</select>
										</div>
									</div>
								</div>
								<div id="div_add_safety">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Alat Kelengkapan Diri (<span class="text-red">Silahkan Isi Jika Masih Kosong</span>)</label>
										<div class="col-sm-10" align="left">
											<textarea id="add_alat_kelengkapan" style="width: 100%"></textarea>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Alat / Mesin yang Digunakan (<span class="text-red">Silahkan Isi Jika Masih Kosong</span>)</label>
										<div class="col-sm-10" align="left">
											<textarea id="add_alat_mesin" style="width: 100%"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#create_modal').modal('hide');"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-2">Document <span class="text-red">*</span></label>
									<div class="col-sm-10" align="left" id="divEditProcess">
										<input type="hidden" name="id" id="id">
										<select class="form-control" onchange="checkDocument(this.value,this.id)" name="edit_document" id="edit_document" data-placeholder="Pilih Document" style="width: 100%;">
											<option></option>
											@foreach($process3 as $process)
											<option value="{{$process->document_number}}">{{$process->document_number}}_{{$process->document_name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Proses Pekerjaan</label>
									<div class="col-sm-10" align="left">
										<textarea id="edit_work_process" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Point Pekerjaan</label>
									<div class="col-sm-10" align="left">
										<textarea id="edit_work_point" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Safety Point</label>
									<div class="col-sm-10" align="left">
										<textarea id="edit_work_safety" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Tipe Jawaban <span class="text-red">*</span></label>
									<div class="col-sm-10" align="left" id="divEditType">
										<select class="form-control" name="edit_audit_type" id="edit_audit_type" data-placeholder="Pilih Tipe Jawaban" style="width: 100%;" onchange="checkJawabanEdit(this.value)">
											<option value="Normal">Normal</option>
											<option value="Isian">Isian</option>
										</select>
									</div>
								</div>
								<div id="div_edit_value">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Batas Bawah</label>
										<div class="col-sm-10" align="left">
											<input type="text" id="edit_lower" style="width: 100%" class="form-control numpad" placeholder="Isi Batas Bawah" readonly>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Batas Atas</label>
										<div class="col-sm-10" align="left">
											<input type="text" id="edit_upper" style="width: 100%" class="form-control numpad" placeholder="Isi Batas Atas" readonly>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">UOM</label>
										<div class="col-sm-10" align="left" id="divEditUom">
											<select class="form-control" name="edit_uom" id="edit_uom" data-placeholder="Pilih UOM" style="width: 100%;">
											<option value=""></option>
											@foreach($uom2 as $uom)
											<option value="{{$uom}}">{{$uom}}</option>
											@endforeach
										</select>
										</div>
									</div>
								</div>
								<div id="div_edit_safety">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Alat Kelengkapan Diri (<span class="text-red">Silahkan Isi Jika Masih Kosong</span>)</label>
										<div class="col-sm-10" align="left">
											<textarea id="edit_alat_kelengkapan" style="width: 100%"></textarea>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Alat / Mesin yang Digunakan (<span class="text-red">Silahkan Isi Jika Masih Kosong</span>)</label>
										<div class="col-sm-10" align="left">
											<textarea id="edit_alat_mesin" style="width: 100%"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit_modal').modal('hide');"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_safety_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Point Safety</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id_safety" id="id_safety">
								<div class="form-group row" align="right">
									<label class="col-sm-2">Document</label>
									<div class="col-sm-10" align="left">
										<input class="form-control" readonly id="edit_document_safety" style="width: 100%">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Category</label>
									<div class="col-sm-10" align="left">
										<input class="form-control" readonly id="edit_category_safety" style="width: 100%">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Point Safety</label>
									<div class="col-sm-10" align="left">
										<textarea id="edit_point_check_safety" style="width: 100%"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit_safety_modal').modal('hide');"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="updateSafety()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>



</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var safety = null;

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('body').toggleClass("sidebar-collapse");

		CKEDITOR.replace('edit_point_check_safety' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

		CKEDITOR.replace('add_work_process' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_work_point' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_work_safety' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_work_process' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_work_point' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_work_safety' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_alat_kelengkapan' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_alat_mesin' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_alat_kelengkapan' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_alat_mesin' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

		// fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		safety = null;

		$("#div_add_safety").hide();
		$("#div_add_value").hide();
		$("#div_edit_value").hide();
	});

	function cancelAll() {
		$("#add_document").val('').trigger('change');
		$("#add_work_process").html(CKEDITOR.instances.add_work_process.setData(''));
		$("#edit_point_check_safety").html(CKEDITOR.instances.edit_point_check_safety.setData(''));
		$("#add_work_point").html(CKEDITOR.instances.add_work_point.setData(''));
		$("#add_work_safety").html(CKEDITOR.instances.add_work_safety.setData(''));
		$("#add_audit_type").val('Normal').trigger('change');
		$("#add_uom").val('').trigger('change');
		$("#add_lower").val('');
		$("#add_upper").val('');

		$("#edit_document").val('').trigger('change');
		$("#edit_work_process").html(CKEDITOR.instances.edit_work_process.setData(''));
		$("#edit_work_point").html(CKEDITOR.instances.edit_work_point.setData(''));
		$("#edit_work_safety").html(CKEDITOR.instances.edit_work_safety.setData(''));
		$("#edit_audit_type").val('Normal').trigger('change');
		$("#edit_uom").val('').trigger('change');
		$("#edit_lower").val('');
		$("#edit_upper").val('');

		$("#add_alat_kelengkapan").html(CKEDITOR.instances.add_alat_kelengkapan.setData(''));
		$("#add_alat_mesin").html(CKEDITOR.instances.add_alat_mesin.setData(''));

		$("#edit_alat_kelengkapan").html(CKEDITOR.instances.edit_alat_kelengkapan.setData(''));
		$("#edit_alat_mesin").html(CKEDITOR.instances.edit_alat_mesin.setData(''));

		$("#div_add_safety").hide();
		$("#div_edit_safety").hide();

		$("#div_add_value").hide();
		$("#div_edit_value").hide();
	}


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
		$('#add_document').select2({
			allowClear:true,
			dropdownParent: $('#divAddProcess'),
		});

		$('#add_audit_type').select2({
			allowClear:true,
			dropdownParent: $('#divAddType'),
		});

		$('#add_uom').select2({
			allowClear:true,
			dropdownParent: $('#divAddUom'),
		});

		$('#edit_document').select2({
			allowClear:true,
			dropdownParent: $('#divEditProcess'),
		});

		$('#edit_audit_type').select2({
			allowClear:true,
			dropdownParent: $('#divEditType'),
		});

		$('#edit_uom').select2({
			allowClear:true,
			dropdownParent: $('#divEditUom'),
		});
	});

	function checkJawaban(value) {
		if (value == 'Isian') {
			$("#div_add_value").show();
		}else{
			$("#div_add_value").hide();
		}
	}

	function checkJawabanEdit(value) {
		if (value == 'Isian') {
			$("#div_edit_value").show();
		}else{
			$("#div_edit_value").hide();
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
	function fillList(){
		$('#loading').show();
		var data = {
			document_number:$('#document_number').val(),
			category:'{{$category}}',
		}
		$.get('{{ url("fetch/qa/special_process/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tablePointCheck').DataTable().clear();
				$('#tablePointCheck').DataTable().destroy();
				$('#bodyTablePointCheck').html("");
				var tableData = "";
				var index = 1;
				$.each(result.point_check, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.document_number +'</td>';
					tableData += '<td>'+ value.document_name +'</td>';
					tableData += '<td>'+ (value.work_process || '') +'</td>';
					tableData += '<td>'+ (value.work_point || '') +'</td>';
					tableData += '<td>'+ (value.work_safety || '') +'</td>';
					tableData += '<td>';
					tableData += (value.audit_type || '');
					if (value.lower != null) {
						tableData += '<br>Lower : '+value.lower;
					}
					if (value.upper != null) {
						tableData += '<br>Lower : '+value.upper;
					}
					if (value.uom != null) {
						tableData += '<br>UOM : '+value.uom;
					}
					tableData += '</td>';
					// var alat_kelengkapan = '';
					// var mesin = '';
					// for(var i = 0; i < result.safety.length;i++){
					// 	if (result.safety[i].document_number == value.document_number && result.safety[i].category_safety == 'Alat Kelengkapan Diri') {
					// 		alat_kelengkapan = result.safety[i].point_safety;
					// 	}
					// 	if (result.safety[i].document_number == value.document_number && result.safety[i].category_safety == 'Alat / Mesin yang Digunakan') {
					// 		mesin = result.safety[i].point_safety;
					// 	}
					// }
					// tableData += '<td>'+ alat_kelengkapan +'</td>';
					// tableData += '<td>'+ mesin +'</td>';
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editPointCheck(\''+value.id+'\')">Edit</button><button class="btn btn-sm btn-danger" style="margin-left:5px;" onclick="deletePointCheck(\''+value.id+'\')">Delete</button></td>';
					tableData += '</tr>';
					index++;
				});

				safety = result.safety;
				$('#bodyTablePointCheck').append(tableData);

				$('#judul').html('Point Check');

				var table = $('#tablePointCheck').DataTable({
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

				$('#tablePointApd').DataTable().clear();
				$('#tablePointApd').DataTable().destroy();

				$('#bodyTableApd').html("");
				var tableData = "";
				var index = 1;
				$.each(result.safety, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.document_number +'</td>';
					tableData += '<td>'+ value.document_name +'</td>';
					tableData += '<td>'+ value.category_safety +'</td>';
					if (value.category_safety == 'Alat Kelengkapan Diri') {
						tableData += '<td>'+ (value.point_safety || '') +'</td>';
						tableData += '<td></td>';
					}else{
						tableData += '<td></td>';
						tableData += '<td>'+ (value.point_safety || '') +'</td>';
					}
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editPointSafety(\''+value.id+'\',\''+value.document_number+'\',\''+value.document_name+'\',\''+value.category_safety+'\',\''+value.point_safety+'\')">Edit</button></td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableApd').append(tableData);

				var table = $('#tablePointApd').DataTable({
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

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function checkDocument(value,id) {
		$("#div_add_safety").hide();
		$("#div_edit_safety").hide();
		var document_number = $('#'+id).val().split('_')[0];
		var alat_kelengkapan = '';
		var mesin = '';
		if (safety != null) {
			for(var i = 0; i < safety.length;i++){
				if (safety[i].document_number == document_number && safety[i].category_safety == 'Alat Kelengkapan Diri') {
					alat_kelengkapan = safety[i].point_safety;
				}
				if (safety[i].document_number == document_number && safety[i].category_safety == 'Alat / Mesin yang Digunakan') {
					mesin = safety[i].point_safety;
				}
			}
		}

		$("#add_alat_kelengkapan").html(CKEDITOR.instances.add_alat_kelengkapan.setData(alat_kelengkapan));;
		$("#add_alat_mesin").html(CKEDITOR.instances.add_alat_mesin.setData(mesin));;
		$("#div_add_safety").show();

		$("#edit_alat_kelengkapan").html(CKEDITOR.instances.edit_alat_kelengkapan.setData(alat_kelengkapan));;
		$("#edit_alat_mesin").html(CKEDITOR.instances.edit_alat_mesin.setData(mesin));;
		$("#div_edit_safety").show();
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#add_document').val() == '' || $('#add_audit_type').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Isi No. Dokumen & Tipe Jawaban');
				return false;
			}
			var document_number = $("#add_document").val().split('_')[0];
			var document_name = $("#add_document").val().split('_')[1];
			var work_process = CKEDITOR.instances.add_work_process.getData();
			var work_point = CKEDITOR.instances.add_work_point.getData();
			var work_safety = CKEDITOR.instances.add_work_safety.getData();
			var category = '{{$category}}';
			var audit_type = $('#add_audit_type').val();
			var lower = $('#add_lower').val();
			var upper = $('#add_upper').val();
			var uom = $('#add_uom').val();

			var add_alat_kelengkapan = CKEDITOR.instances.add_alat_kelengkapan.getData();
			var add_alat_mesin = CKEDITOR.instances.add_alat_mesin.getData();

			var data = {
				document_number:document_number,
				document_name:document_name,
				work_process:work_process,
				work_point:work_point,
				work_safety:work_safety,
				add_alat_kelengkapan:add_alat_kelengkapan,
				add_alat_mesin:add_alat_mesin,
				category:category,
				audit_type:audit_type,
				lower:lower,
				upper:upper,
				uom:uom,
			}

			$.post('{{ url("input/qa/special_process/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#create_modal').modal('hide');
					openSuccessGritter('Success!',result.message);
					fillList();
					cancelAll();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}

	function deletePointCheck(id) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("delete/qa/special_process/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!',result.message);
					fillList();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}

	function editPointCheck(id) {
		$('#loading').show();
		var data = {
			id:id
		}

		$.get('{{ url("edit/qa/special_process/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#edit_modal').modal('show');
				$('#id').val(id);
				$('#edit_document').val(result.point.document_number).trigger('change');
				$("#edit_work_process").html(CKEDITOR.instances.edit_work_process.setData(result.point.work_process));
				// $('#edit_point_check').val(result.point.point_check);
				$("#edit_work_point").html(CKEDITOR.instances.edit_work_point.setData(result.point.work_point));
				$("#edit_work_safety").html(CKEDITOR.instances.edit_work_safety.setData(result.point.work_safety));
				if (result.point.audit_type == null) {
					$('#edit_audit_type').val('Normal').trigger('change');
				}else{
					$('#edit_audit_type').val(result.point.audit_type).trigger('change');
					$('#edit_uom').val(result.point.uom).trigger('change');
					$('#edit_lower').val(result.point.lower);
					$('#edit_upper').val(result.point.upper);
				}
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				$('#loading').hide();
			}
		});
	}

	function editPointSafety(id,document_number,document_name,category_safety,point_check) {
		$('#loading').show();
		$("#edit_point_check_safety").html(CKEDITOR.instances.edit_point_check_safety.setData(point_check));
		$("#edit_category_safety").val(category_safety);
		$("#id_safety").val(id);
		$("#edit_document_safety").val(document_number +' - '+document_name);
		$('#loading').hide();
		$('#edit_safety_modal').modal('show');
	}

	function updateSafety() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var id = $("#id_safety").val();
			var point_safety = CKEDITOR.instances.edit_point_check_safety.getData();

			var data = {
				point_safety:point_safety,
				id:id,
			}

			$.post('{{ url("update/qa/special_process/point_safety") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#edit_safety_modal').modal('hide');
					openSuccessGritter('Success!',result.message);
					fillList();
					cancelAll();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#edit_document').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Isi No. Dokumen');
				return false;
			}
			var document_number = $("#edit_document option:selected").text().split('_')[0];
			var document_name = $("#edit_document option:selected").text().split('_')[1];
			var id = $("#id").val();
			var work_process = CKEDITOR.instances.edit_work_process.getData();
			var work_point = CKEDITOR.instances.edit_work_point.getData();
			var work_safety = CKEDITOR.instances.edit_work_safety.getData();
			var category = '{{$category}}';
			var audit_type = $('#edit_audit_type').val();
			var lower = $('#edit_lower').val();
			var upper = $('#edit_upper').val();
			var uom = $('#edit_uom').val();

			var edit_alat_kelengkapan = CKEDITOR.instances.edit_alat_kelengkapan.getData();
			var edit_alat_mesin = CKEDITOR.instances.edit_alat_mesin.getData();

			var data = {
				document_number:document_number,
				document_name:document_name,
				work_process:work_process,
				work_point:work_point,
				work_safety:work_safety,
				category:category,
				edit_alat_kelengkapan:edit_alat_kelengkapan,
				edit_alat_mesin:edit_alat_mesin,
				id:id,
				audit_type:audit_type,
				lower:lower,
				upper:upper,
				uom:uom,
			}

			$.post('{{ url("update/qa/special_process/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#edit_modal').modal('hide');
					openSuccessGritter('Success!',result.message);
					fillList();
					cancelAll();
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}



</script>
@endsection