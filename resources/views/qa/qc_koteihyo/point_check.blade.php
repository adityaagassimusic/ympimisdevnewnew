@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
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

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" onclick="clearAll()" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point Check</button>
		<button class="btn btn-info btn-sm pull-right" data-toggle="modal"  data-target="#upload_modal" onclick="clearAll()" style="margin-right: 5px">
			<i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Upload Excel</button>
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
					<center><h4 id="judul"></h4></center>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Document</span>
							<div class="form-group">
								<select class="form-control select2" name="document_number" id="document_number" data-placeholder="Pilih Document" style="width: 100%;">
									<option></option>
									@foreach($qc_koteihyo as $qc_koteihyo)
									<option value="{{$qc_koteihyo->document_number}}">{{$qc_koteihyo->document_number}} - {{$qc_koteihyo->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/qc_koteihyo/') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/qc_koteihyo/point_check/') }}" class="btn btn-danger">Clear</a>
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
										<th width="1%">Document</th>
										<th width="1%">Title</th>
										<th width="1%">Flow</th>
										<th width="1%">Process</th>
										<th width="1%">Category</th>
										<th width="1%">Jaminan Mutu</th>
										<th width="1%">Point Pemeriksaan</th>
										<th width="1%">Khusus</th>
										<th width="1%">No. Instruksi Kerja</th>
										<th width="1%">Spesifikasi Standar</th>
										<th width="1%">Cara Kontrol</th>
										<th width="1%">Jumlah / Frekuensi</th>
										<th width="1%">Mesin</th>
										<th width="1%">Jig</th>
										<th width="1%">Data Kontrol</th>
										<th width="1%">Pemeriksa</th>
										<th width="1%">Keterangan</th>
										<th width="1%">Images</th>
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
									<label class="col-sm-3">Document <span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="divAddProcess">
										<select class="form-control" name="add_document" id="add_document" data-placeholder="Pilih Document" style="width: 100%;">
											<option value=""></option>
											@foreach($qc_koteihyo2 as $qc_koteihyo)
											<option value="{{$qc_koteihyo->document_number}}_{{$qc_koteihyo->title}}">{{$qc_koteihyo->document_number}} - {{$qc_koteihyo->title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Category <span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="divAddCategory">
										<select class="form-control" name="add_category" id="add_category" data-placeholder="Pilih Category" style="width: 100%;">
											<option value=""></option>
											<option value="Ketentuan Produk / Material">Ketentuan Produk / Material</option>
											<option value="Ketentuan Proses">Ketentuan Proses</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Process Number</label>
									<div class="col-sm-9" align="left">
										<input type="number" class="form-control numpad" id="add_process_number" placeholder="Process Number" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Process</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_process" placeholder="Process">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jaminan Mutu</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_jaminan_mutu" placeholder="Jaminan Mutu">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Point Check</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_point_check" placeholder="Point Check">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Khusus (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_specifics" placeholder="Khusus (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">No. Instruksi Kerja</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_instruction_number" placeholder="No. Instruksi Kerja">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Standard</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_standard" placeholder="Standard">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Cara Kontrol</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_control_way" placeholder="Cara Kontrol">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jumlah / Frekuensi</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_frequency" placeholder="Jumlah / Frekuensi">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Mesin (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_machine" placeholder="Mesin (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jig (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_jig" placeholder="Jig (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Data Kontrol</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_control_data" placeholder="Data Kontrol">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Pemeriksa</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_checker" placeholder="Pemeriksa">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Keterangan</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="add_keterangan" placeholder="Keterangan">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Images</label>
									<div class="col-sm-9" align="left">
										<input type="file" id="add_images" accept="image/*" capture="environment">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#create_modal').modal('hide')"><i class="fa fa-close"></i> Close</button>
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
									<label class="col-sm-3">Document <span class="text-red">*</span></label>
									<input type="hidden" name="id" id="id">
									<div class="col-sm-9" align="left" id="divAddProcess">
										<select class="form-control" name="edit_document" id="edit_document" data-placeholder="Pilih Document" style="width: 100%;">
											<option value=""></option>
											@foreach($qc_koteihyo3 as $qc_koteihyo)
											<option value="{{$qc_koteihyo->document_number}}_{{$qc_koteihyo->title}}">{{$qc_koteihyo->document_number}} - {{$qc_koteihyo->title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Category <span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="divAddCategory">
										<select class="form-control" name="edit_category" id="edit_category" data-placeholder="Pilih Category" style="width: 100%;">
											<option value=""></option>
											<option value="Ketentuan Produk / Material">Ketentuan Produk / Material</option>
											<option value="Ketentuan Proses">Ketentuan Proses</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Process Number</label>
									<div class="col-sm-9" align="left">
										<input type="number" class="form-control numpad" id="edit_process_number" placeholder="Process Number" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Process</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_process" placeholder="Process">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jaminan Mutu</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_jaminan_mutu" placeholder="Jaminan Mutu">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Point Check</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_point_check" placeholder="Point Check">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Khusus (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_specifics" placeholder="Khusus (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">No. Instruksi Kerja</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_instruction_number" placeholder="No. Instruksi Kerja">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Standard</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_standard" placeholder="Standard">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Cara Kontrol</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_control_way" placeholder="Cara Kontrol">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jumlah / Frekuensi</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_frequency" placeholder="Jumlah / Frekuensi">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Mesin (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_machine" placeholder="Mesin (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Jig (<span class="text-green">Ketentuan Proses</span>)</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_jig" placeholder="Jig (Ketentuan Proses)">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Data Kontrol</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_control_data" placeholder="Data Kontrol">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Pemeriksa</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_checker" placeholder="Pemeriksa">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Keterangan</label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="edit_keterangan" placeholder="Keterangan">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">Images</label>
									<div class="col-sm-9" align="left">
										<input type="file" id="edit_images" accept="image/*" capture="environment">
									</div>
									<div class="col-sm-9 col-offset-3" align="left" id="div_img">
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit_modal').modal('hide')"><i class="fa fa-close"></i> Close</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="upload_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Upload Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-3">Document <span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="divAddDocumentUpload">
										<select class="form-control" name="upload_document" id="upload_document" data-placeholder="Pilih Document" style="width: 100%;">
											<option value=""></option>
											@foreach($qc_koteihyo2 as $qc_koteihyo)
											<option value="{{$qc_koteihyo->document_number}}_{{$qc_koteihyo->title}}">{{$qc_koteihyo->document_number}} - {{$qc_koteihyo->title}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-sm-3">
									</div>
									<div class="col-sm-9">
										<a class="btn btn-info pull-left" href="{{url('download/qa/qc_koteihyo/point_check')}}">Example</a>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-3">File Excel</label>
									<div class="col-sm-9" align="left">
										<input type="file" id="file_excel" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#upload_modal').modal('hide')"><i class="fa fa-close"></i> Close</button>
					<button class="btn btn-success" onclick="upload()"><i class="fa fa-file-excel-o"></i> Upload</button>
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
		clearAll();
		$('body').toggleClass("sidebar-collapse");

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
	});

	function clearAll() {
		$("#add_document").val('').trigger('change');
		$("#add_category").val('').trigger('change');
		$("#add_process").val('');
		$("#add_process_number").val('');
		$("#add_jaminan_mutu").val('');
		$("#add_point_check").val('');
		$("#add_specifics").val('');
		$("#add_instruction_number").val('');
		$("#add_standard").val('');
		$("#add_control_way").val('');
		$("#add_frequency").val('');
		$("#add_machine").val('');
		$("#add_jig").val('');
		$("#add_control_data").val('');
		$("#add_checker").val('');
		$("#add_images").val('');
		$("#add_keterangan").val('');

		$("#edit_document").val('').trigger('change');
		$("#edit_category").val('').trigger('change');
		$("#edit_process").val('');
		$("#edit_process_number").val('');
		$("#edit_jaminan_mutu").val('');
		$("#edit_point_check").val('');
		$("#edit_specifics").val('');
		$("#edit_instruction_number").val('');
		$("#edit_standard").val('');
		$("#edit_control_way").val('');
		$("#edit_frequency").val('');
		$("#edit_machine").val('');
		$("#edit_jig").val('');
		$("#edit_control_data").val('');
		$("#edit_checker").val('');
		$("#edit_images").val('');
		$("#edit_keterangan").val('');

		$("#upload_document").val('');
		$("#file_excel").val('');
	}


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
		$('#add_document').select2({
			allowClear:true,
			dropdownParent: $('#divAddProcess'),
		});

		$('#add_category').select2({
			allowClear:true,
			dropdownParent: $('#divAddCategory'),
		});

		$('#edit_document').select2({
			allowClear:true,
			dropdownParent: $('#divEditProcess'),
		});

		$('#edit_category').select2({
			allowClear:true,
			dropdownParent: $('#divEditCategory'),
		});

		$('#upload_document').select2({
			allowClear:true,
			dropdownParent: $('#divAddDocumentUpload'),
		});
	});

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
		}
		$.get('{{ url("fetch/qa/qc_koteihyo/point_check") }}',data, function(result, status, xhr){
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
					tableData += '<td>'+ value.process_number +'</td>';
					tableData += '<td>'+ (value.process || '') +'</td>';
					tableData += '<td>'+ (value.category || '') +'</td>';
					tableData += '<td>'+ (value.jaminan_mutu || '') +'</td>';
					tableData += '<td>'+ (value.point_check || '') +'</td>';
					tableData += '<td>'+ (value.specifics || '') +'</td>';
					tableData += '<td>'+ (value.instruction_number || '') +'</td>';
					tableData += '<td>'+ (value.standard || '') +'</td>';
					tableData += '<td>'+ (value.control_way || '') +'</td>';
					tableData += '<td>'+ (value.frequency || '') +'</td>';
					tableData += '<td>'+ (value.machine || '') +'</td>';
					tableData += '<td>'+ (value.jig || '') +'</td>';
					tableData += '<td>'+ (value.control_data || '') +'</td>';
					tableData += '<td>'+ (value.checker || '') +'</td>';
					tableData += '<td>'+ (value.keterangan || '') +'</td>';
					var url = '{{url("data_file/qa/qc_koteihyo/point_check/")}}'+'/'+value.images;
					if (value.images == null || value.images == "") {
						tableData += '<td></td>';
					}else{
						tableData += '<td><a target="_blank" href="'+url+'"><img style="width:100px" src="'+url+'"></a></td>';
					}
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editPointCheck(\''+value.id+'\')">Edit</button><button class="btn btn-sm btn-danger" style="margin-left:5px;" onclick="deletePointCheck(\''+value.id+'\')">Delete</button></td>';
					tableData += '</tr>';
					index++;
				});
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

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show()
			var document_number = $("#add_document").val().split('_')[0];
			var document_name = $("#add_document").val().split('_')[1];
			var category = $("#add_category").val();
			var processes = $("#add_process").val();
			var process_number = $("#add_process_number").val();
			var jaminan_mutu = $("#add_jaminan_mutu").val();
			var point_check = $("#add_point_check").val();
			var specifics = $("#add_specifics").val();
			var instruction_number = $("#add_instruction_number").val();
			var standard = $("#add_standard").val();
			var control_way = $("#add_control_way").val();
			var frequency = $("#add_frequency").val();
			var machine = $("#add_machine").val();
			var jig = $("#add_jig").val();
			var control_data = $("#add_control_data").val();
			var checker = $("#add_checker").val();
			var keterangan = $("#add_keterangan").val();

			var formData = new FormData();
			var newAttachment  = $('#add_images').prop('files')[0];
			var file = $('#add_images').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('images', newAttachment);
			formData.append('document_number',document_number);
			formData.append('document_name',document_name);
			formData.append('category',category);
			formData.append('process',processes);
			formData.append('process_number',process_number);
			formData.append('jaminan_mutu',jaminan_mutu);
			formData.append('point_check',point_check);
			formData.append('specifics',specifics);
			formData.append('instruction_number',instruction_number);
			formData.append('standard',standard);
			formData.append('control_way',control_way);
			formData.append('frequency',frequency);
			formData.append('machine',machine);
			formData.append('jig',jig);
			formData.append('control_data',control_data);
			formData.append('checker',checker);
			formData.append('keterangan',keterangan);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('input/qa/qc_koteihyo/point_check') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success!',data.message);
						clearAll();
						$('#create_modal').modal('hide');
						$('#loading').hide();
						fillList();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

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

			$.get('{{ url("delete/qa/qc_koteihyo/point_check") }}',data, function(result, status, xhr){
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
		clearAll();
		$('#loading').show();
		var data = {
			id:id
		}

		$.get('{{ url("edit/qa/qc_koteihyo/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#edit_modal').modal('show');

				$('#id').val(id);
				$("#edit_document").val(result.point.document_number+'_'+result.point.document_name).trigger('change');
				$("#edit_category").val(result.point.category).trigger('change');
				$("#edit_process").val(result.point.process);
				$("#edit_process_number").val(result.point.process_number);
				$("#edit_jaminan_mutu").val(result.point.jaminan_mutu);
				$("#edit_point_check").val(result.point.point_check);
				$("#edit_specifics").val(result.point.specifics);
				$("#edit_instruction_number").val(result.point.instruction_number);
				$("#edit_standard").val(result.point.standard);
				$("#edit_control_way").val(result.point.control_way);
				$("#edit_frequency").val(result.point.frequency);
				$("#edit_machine").val(result.point.machine);
				$("#edit_jig").val(result.point.jig);
				$("#edit_control_data").val(result.point.control_data);
				$("#edit_checker").val(result.point.checker);
				$("#edit_keterangan").val(result.point.keterangan);

				$('#div_img').html('');
				if (result.point.images != '' && result.point.images != null) {
					var url = '{{url("data_file/qa/qc_koteihyo/point_check/")}}'+'/'+result.point.images;
					$('#div_img').html('<img src="'+url+'" style="width:100px">');
				}
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				$('#loading').hide();
			}
		});
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show()
			var document_number = $("#edit_document").val().split('_')[0];
			var document_name = $("#edit_document").val().split('_')[1];
			var category = $("#edit_category").val();
			var processes = $("#edit_process").val();
			var process_number = $("#edit_process_number").val();
			var jaminan_mutu = $("#edit_jaminan_mutu").val();
			var point_check = $("#edit_point_check").val();
			var specifics = $("#edit_specifics").val();
			var instruction_number = $("#edit_instruction_number").val();
			var standard = $("#edit_standard").val();
			var control_way = $("#edit_control_way").val();
			var frequency = $("#edit_frequency").val();
			var machine = $("#edit_machine").val();
			var jig = $("#edit_jig").val();
			var control_data = $("#edit_control_data").val();
			var checker = $("#edit_checker").val();
			var keterangan = $("#edit_keterangan").val();
			var id = $("#id").val();

			var formData = new FormData();
			var newAttachment  = $('#edit_images').prop('files')[0];
			var file = $('#edit_images').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('images', newAttachment);
			formData.append('id',id);
			formData.append('document_number',document_number);
			formData.append('document_name',document_name);
			formData.append('category',category);
			formData.append('process',processes);
			formData.append('process_number',process_number);
			formData.append('jaminan_mutu',jaminan_mutu);
			formData.append('point_check',point_check);
			formData.append('specifics',specifics);
			formData.append('instruction_number',instruction_number);
			formData.append('standard',standard);
			formData.append('control_way',control_way);
			formData.append('frequency',frequency);
			formData.append('machine',machine);
			formData.append('jig',jig);
			formData.append('control_data',control_data);
			formData.append('checker',checker);
			formData.append('keterangan',keterangan);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('update/qa/qc_koteihyo/point_check') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success!',data.message);
						clearAll();
						$('#edit_modal').modal('hide');
						$('#loading').hide();
						fillList();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
		}
	}

	function upload() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#upload_document').val() == '' || $('#file_excel').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
				openErrorGritter('Error!','Isi Semua Data');
				$('#loading').hide();
				return false;
			}

			var document_number = $("#upload_document").val().split('_')[0];
			var document_name = $("#upload_document").val().split('_')[1];

			var formData = new FormData();
			var newAttachment  = $('#file_excel').prop('files')[0];
			var file = $('#file_excel').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('file_excel', newAttachment);
			formData.append('document_number',document_number);
			formData.append('document_name',document_name);

			$.ajax({
				url:"{{ url('upload/qa/qc_koteihyo/point_check') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success!',data.message);
						clearAll();
						$('#upload_modal').modal('hide');
						$('#loading').hide();
						fillList();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
		}
	}



</script>
@endsection