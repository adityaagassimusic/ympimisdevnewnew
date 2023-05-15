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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Document
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
							<table id="tableDocument" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 10%">QC Koteihyo</th>
										<th style="width: 10%">Dokumen IK</th>
										<th style="width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableDocument">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Document</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Document QC Koteihyo<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditDocumentQcKoteihyo">
										<select class="form-control select3" data-placeholder="Select Document QC Koteihyo" name="edit_document_qc_koteihyo" id="edit_document_qc_koteihyo" style="width: 100%">
											<option value=""></option>
											@foreach($all_doc as $all_doc)
											@if($all_doc->category == 'DM')
											<option value="{{$all_doc->document_number}}">{{$all_doc->document_number}} - {{$all_doc->title}}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Document IK<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditDocumentIK">
										<select class="form-control select3" multiple data-placeholder="Select Document IK" name="select_edit_document_ik" id="select_edit_document_ik" style="width: 100%" onchange="changeEditDocumentIK()">
											@foreach($all_doc2 as $all_doc2)
											@if($all_doc2->category == 'IK')
											<option value="{{$all_doc2->document_number}}">{{$all_doc2->document_number}} - {{$all_doc2->title}}</option>
											@endif
											@endforeach
										</select>
										<input type="hidden" name="edit_document_ik" id="edit_document_ik">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
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
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Document</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Document QC Koteihyo<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddDocumentQcKoteihyo">
										<select class="form-control select3" data-placeholder="Select Document QC Koteihyo" name="add_document_qc_koteihyo" id="add_document_qc_koteihyo" style="width: 100%">
											<option value=""></option>
											@foreach($all_doc3 as $all_doc3)
											@if($all_doc3->category == 'DM')
											<option value="{{$all_doc3->document_number}}">{{$all_doc3->document_number}} - {{$all_doc3->title}}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Document IK<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddDocumentIK">
										<select class="form-control select3" multiple data-placeholder="Select Document Ik" name="select_add_document_ik" id="select_add_document_ik" style="width: 100%" onchange="changeAddDocumentIK()">
											@foreach($all_doc4 as $all_doc4)
											@if($all_doc4->category == 'IK')
											<option value="{{$all_doc4->document_number}}">{{$all_doc4->document_number}} - {{$all_doc4->title}}</option>
											@endif
											@endforeach
										</select>
										<input type="hidden" name="add_document_ik" id="add_document_ik">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
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

	function changeAddDocumentIK() {
		$("#add_document_ik").val($("#select_add_document_ik").val());
	}

	function changeEditDocumentIK() {
		$("#edit_document_ik").val($("#select_edit_document_ik").val());
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.datepicker').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		$('#add_document_qc_koteihyo').select2({
			allowClear:true,
			dropdownParent: $('#divAddDocumentQcKoteihyo'),
		});
		$('#select_add_document_ik').select2({
			allowClear:true,
			dropdownParent: $('#divAddDocumentIK'),
		});
		$('#edit_document_qc_koteihyo').select2({
			allowClear:true,
			dropdownParent: $('#divEditDocumentQcKoteihyo'),
		});
		$('#select_edit_document_ik').select2({
			allowClear:true,
			dropdownParent: $('#divEditDocumentIK'),
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fillData(){
		$('#loading').show();
		
		$.get('{{ url("fetch/audit_report_activity/document") }}', function(result, status, xhr){
			if(result.status){
				if (result.document != null) {
					$('#tableDocument').DataTable().clear();
					$('#tableDocument').DataTable().destroy();
					$('#bodyTableDocument').html("");
					var tableDocument = "";
					
					var index = 1;

					$.each(result.document, function(key, value) {
						tableDocument += '<tr>';
						tableDocument += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						var docname = '';
						for(var i = 0; i < result.document_all.length;i++){
							if (result.document_all[i].document_number == value.document_number_qc_koteihyo) {
								docname = result.document_all[i].title;
							}
						}
						tableDocument += '<td style="text-align:left;padding-left:7px;">'+value.document_number_qc_koteihyo+'<br>'+docname+'</td>';
						tableDocument += '<td style="text-align:left;padding-left:7px;">';
						var docname = '';
						if (value.document_number_ik.match(/,/gi)) {
							var document_ik = value.document_number_ik.split(',');
							for(var j = 0; j < document_ik.length;j++){
								for(var i = 0; i < result.document_all.length;i++){
									if (result.document_all[i].document_number == document_ik[j]) {
										docname += result.document_all[i].document_number+' - '+result.document_all[i].title+'<br>';
									}
								}
							}
						}else{
							for(var i = 0; i < result.document_all.length;i++){
								if (result.document_all[i].document_number == value.document_number_ik) {
									docname += result.document_all[i].document_number+' - '+result.document_all[i].title+'<br>';
								}
							}
						}
						tableDocument += docname+'</td>';
						tableDocument += '<td style="text-align:center"><button class="btn btn-warning btn-sm" onclick="editDocument(\''+value.id+'\',\''+value.document_number_qc_koteihyo+'\',\''+value.document_name_qc_koteihyo+'\',\''+value.document_number_ik+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteDocument(\''+value.id+'\')"><i class="fa fa-trash"></i></button></td>';
						tableDocument += '</tr>';
						index++;
					});
					$('#bodyTableDocument').append(tableDocument);

					var table = $('#tableDocument').DataTable({
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

	const hexToDecimal = hex => parseInt(hex, 16);

	function editDocument(id,document_number_qc_koteihyo,document_name_qc_koteihyo,document_number_ik) {
		$('#id').val(id);
		$('#edit_document_qc_koteihyo').val(document_number_qc_koteihyo).trigger('change');
		$('#id').val(id);
		$('#select_edit_document_ik').val(document_number_ik.split(',')).trigger('change');
		$('#edit_document_ik').val(document_number_ik).trigger('change');
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			if ($('#edit_document_qc_koteihyo').val() == '' || $('#edit_document_ik').val() == '') {
				openErrorGritter('Error!','Semua Harus Diisi');
				$('#loading').hide();
				return false;
			}
			var data = {
				document_number_qc_koteihyo:$('#edit_document_qc_koteihyo').val(),
				document_number_ik:$('#edit_document_ik').val(),
				id:$('#id').val()
			}

			$.post('{{ url("update/audit_report_activity/document") }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Update Document');
					$("#edit-modal").modal('hide');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			if ($('#add_document_qc_koteihyo').val() == '' || $('#add_document_ik').val() == '') {
				openErrorGritter('Error!','Semua Harus Diisi');
				$('#loading').hide();
				return false;
			}
			var data = {
				document_number_qc_koteihyo:$('#add_document_qc_koteihyo').val(),
				document_number_ik:$('#add_document_ik').val(),
			}

			$.post('{{ url("input/audit_report_activity/document") }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Tambah Document');
					$("#add-modal").modal('hide');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
	}

	function deleteDocument(id) {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			var data = {
				id:id,
			}

			$.get('{{ url("delete/audit_report_activity/document") }}/'+id,data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Delete Document');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
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
</script>
@endsection