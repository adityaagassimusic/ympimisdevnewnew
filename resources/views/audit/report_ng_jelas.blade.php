@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

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
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Date To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Claim</span>
							<div class="form-group">
								<select class="form-control select2" name="audit_title" id="audit_title" data-placeholder="Pilih Claim" style="width: 100%;">
									<option></option>
									@foreach($audit_title as $audit_title)
										<option value="{{$audit_title->audit_id}}">{{$audit_title->audit_title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Condition</span>
							<div class="form-group">
								<select class="form-control select2" name="condition" id="condition" data-placeholder="Pilih Kondisi" style="width: 100%;">
									<option></option>
									<option value="OK">&#9711;</option>
									<option value="NS">&#8420;</option>
									<option value="NG">&#9747;</option>
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/audit_ng_jelas') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/audit_ng_jelas_report') }}" class="btn btn-danger">Clear</a>
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
							<table id="tableAudit" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Date</th>
										<th width="2%">Claim</th>
										<th width="3%">Point Check</th>
										<th width="2%">Image Reference</th>
										<th width="1%">Condition</th>
										<th width="2%">Image Evidence</th>
										<th width="1%">Description</th>
										<th width="1%">Auditor</th>
										<th width="3%">Penanganan</th>
										<th width="1%">Ditangani Oleh</th>
										<th width="1%">Tanggal Penanganan</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableAudit">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Audit NG Jelas</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-2">Claim<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="hidden" class="form-control" id="audit_id" placeholder="Claim Title" readonly>
										<input type="hidden" class="form-control" id="audit_index" placeholder="Claim Title" readonly>
										<input type="claim" class="form-control" id="claim" placeholder="Claim Title" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Auditor<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="auditor" class="form-control" id="auditor" placeholder="Auditor" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Image Reference<span class="text-red">*</span></label>
									<div class="col-sm-5" id="image_reference" align="left">
										
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Condition<span class="text-red">*</span></label>
									<div class="col-sm-2">
										<label class="containers">&#9711;
										  <input type="radio" name="condition" id="condition" value="OK">
										  <span class="checkmark"></span>
										</label>
										<label class="containers">&#9747;
										  <input type="radio" name="condition" id="condition" value="NG">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Image Evidence<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="image_evidence">
										
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Upload Evidence<br><span style="color: red;font-weight: bold;">(Jika ada perubahan foto)</span><span class="text-red">*</span></label>
									<div class="col-sm-10" align="left" id="increment">
										<input type="hidden" id="nomor" value="1">
										<input type="file" id="file_1" accept="image/*" capture="environment">
										<button class="btn btn-success pull-left" onclick="plusImage()"><i class="fa fa-plus"></i></button>
									</div>
									<div align="left" id="clone" class="hide">
										<div class="col-sm-10 col-sm-offset-2" align="left">
											<input type="file" id="file_0" accept="image/*" capture="environment">
											<button class="btn btn-danger pull-left" onclick="minusImage()" id="btn_kurang_0"><i class="fa fa-minus"></i></button>
										</div>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-2">Note<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<textarea id="note" style="width: 500px"></textarea>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
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
			tanggal_from:$('#tanggal_from').val(),
			tanggal_to:$('#tanggal_to').val(),
			audit_title:$('#audit_title').val(),
			condition:$('#condition').val(),
		}
		$.get('{{ url("fetch/qa/audit_ng_jelas_report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableAudit').DataTable().clear();
				$('#tableAudit').DataTable().destroy();
				$('#bodyTableAudit').html("");
				var tableData = "";
				var index = 1;
				$.each(result.audit, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.created +'</td>';
					tableData += '<td>'+ value.audit_title +'</td>';
					tableData += '<td>'+ value.audit_point +'</td>';
					if (value.audit_images == 'null') {
						tableData += '<td style="padding:2px"></td>';
					}else{
						var url_audit = "{{ url('data_file/qa/ng_jelas_point/') }}"+'/'+value.audit_images;
						tableData += '<td style="padding:2px"><img width="100px" src="'+url_audit+'"></td>';
					}
					if (value.result_check == 'OK') {
						tableData += '<td style="background-color:#a2ff8f">&#9711;</td>';
					}else if (value.result_check == 'NG') {
						tableData += '<td style="background-color:#ff8f8f">&#9747;</td>';
					}else if (value.result_check == 'NS'){
						tableData += '<td style="background-color:#fff68f">&#8420;</td>';
					}else{
						tableData += '<td>-</td>';
					}
					if (value.result_image == null) {
						tableData += '<td style="padding:2px"></td>';
					}else{
						tableData += '<td style="padding:2px">';
						if (value.result_image.match(/,/gi)) {
							var images = value.result_image.split(',');
							for(var i = 0; i < images.length;i++){
								var url_result = "{{ url('data_file/qa/ng_jelas/') }}"+'/'+images[i];
								tableData += '<img width="100px" src="'+url_result+'">';
							}
						}else{
							var url_result = "{{ url('data_file/qa/ng_jelas/') }}"+'/'+value.result_image;
							tableData += '<img width="100px" src="'+url_result+'">';
						}
						tableData += '</td>';
					}
					tableData += '<td>'+ (value.note || "") +'</td>';
					tableData += '<td>'+ value.auditor +'<br>'+ value.name.replace(/(.{14})..+/, "$1&hellip;") +'</td>';
					tableData += '<td>'+ (value.handling || "") +'</td>';
					if (value.handled_by == null) {
						tableData += '<td></td>';
					}else{
						tableData += '<td>'+ value.handled_by +'<br>'+value.handled_by_name+'</td>';
					}
					tableData += '<td>'+ (value.handled_at || "") +'</td>';
					tableData += '<td>';
					if ((value.result_check == 'NG' && value.send_status == null) || (value.result_check == 'NS' && value.send_status == null)) {
						tableData += '<button class="btn btn-sm btn-info" onclick="sendEmail(\''+value.id+'\',\''+value.chief_foreman+'\',\''+value.manager+'\')">Send Email</button>';
					}
					tableData += '<button class="btn btn-sm btn-warning" onclick="editAudit(\''+value.id+'\')">Edit</button>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableAudit').append(tableData);

				$('#judul').html('Audit NG Jelas<br>'+result.dateTitleFirst+' - '+result.dateTitleLast);

				var table = $('#tableAudit').DataTable({
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

	function editAudit(id) {
		$('#loading').show();
		var data = {
			id:id,
		}
		$.get('{{ url("edit/qa/audit_ng_jelas") }}',data, function(result, status, xhr){
			if(result.status){
				$("#audit_id").val(id);
				$("#audit_index").val(result.audit.audit_index);
				$("#claim").val(result.audit.audit_title);
				$("#auditor").val(result.audit.auditor+' - '+result.audit.name);
				$("input[name=condition][value=" + result.audit.result_check + "]").prop('checked', true);
				$("#note").val(result.audit.note);
				$('#image_reference').html('');
				var url = '{{url("data_file/qa/ng_jelas_point/")}}'+'/'+result.audit.audit_images;
				$('#image_reference').html('<img style="width:200px;" src="'+url+'" class="user-image" alt="Image Reference Not Available">');
				$('#image_evidence').html('');
				var image_evidence = '';
				if (result.audit.result_image != null) {
					if (result.audit.result_image.match(/,/gi)) {
						var evidence = result.audit.result_image.split(',');
						for(var i = 0; i < evidence.length;i++){
							var url = '{{url("data_file/qa/ng_jelas/")}}'+'/'+evidence[i];
							image_evidence += '<img style="width:200px;" src="'+url+'" class="user-image" alt="Image Evidence Not Available"><br>';
						}
					}else{
						var url = '{{url("data_file/qa/ng_jelas/")}}'+'/'+result.audit.result_image;
						image_evidence += '<img style="width:200px;" src="'+url+'" class="user-image" alt="Image Evidence Not Available">';
					}
				}
				$('#image_evidence').html(image_evidence);
				$('#loading').hide();
				$('#edit-modal').modal('show');
			}else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function plusImage() {
		var html = $("#clone").html();
		$("#increment").after(html);
		$('#file_0').prop('id','file_'+(parseInt($('#nomor').val())+1));
		$('#btn_kurang_0').prop('id','btn_kurang_'+(parseInt($('#nomor').val())+1));
		document.getElementById('btn_kurang_'+(parseInt($('#nomor').val())+1)).setAttribute("onclick", 'minusImage(\''+(parseInt($('#nomor').val())+1)+'\')');
		$('#nomor').val(parseInt($('#nomor').val())+1);
	}

	function minusImage(iii) {
		$('#file_'+iii).remove();
		$('#blah_'+iii).remove();
		$('#btn_kurang_'+iii).remove();
	}

	function update() {
		if (confirm('Apakah Anda yakin akan mengubah data?')) {
			$('#loading').show();
			var stat = 0;
			var result_check = '';
			$("input[name='condition']:checked").each(function (i) {
	            result_check = $(this).val();
	        });
	        if (result_check == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Isi Semua Data');
				return false;
			}
	        var nomor = $('#nomor').val();
	        var audit_id = $('#audit_id').val();
	        var audit_index = $('#audit_index').val();
	        var note = $('#note').val();
	        var filenames = [];
	        if (nomor == 1) {
				var stat_image_ready = 1;
				var stat_image = 1;
				var fileData  = $('#file_1').prop('files')[0];

				file=$('#file_1').val().replace(/C:\\fakepath\\/i, '').split(".");

				var formData = new FormData();
				formData.append('fileData', fileData);
				formData.append('audit_id', audit_id);
				formData.append('result_check', result_check);
				formData.append('extension', file[1]);
				formData.append('foto_name', file[0]);
				formData.append('filename','1_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
				filenames.push('1_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
				
				$.ajax({
					url:"{{ url('upload/file/audit_ng_jelas') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success:function(data)
					{
						filenames.push(data.filename);
						stat_image++;
					},
					error: function(data) {
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
					}
				});
				if (stat_image_ready == stat_image) {
					var formData = new FormData();
					formData.append('audit_id', audit_id);
					formData.append('audit_index', audit_index);
					formData.append('result_check', result_check);
					formData.append('note', note);
					if (filenames.length == 1) {
						formData.append('filenames', filenames[0]);
					}else{
						formData.append('filenames', filenames.join(','));
					}

					$.ajax({
						url:"{{ url('update/qa/audit_ng_jelas') }}",
						method:"POST",
						data:formData,
						dataType:'JSON',
						contentType: false,
						cache: false,
						processData: false,
						success:function(data)
						{
							if (data.status == false) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
							}else if(data.status == true){
								stat++;
							}
							$('#loading').hide();
							openSuccessGritter('Success!','Update Data Success');
							location.reload();
						},
						error: function(data) {
							$('#loading').hide();
							openErrorGritter('Error!',data.message);
							return false;
						}
					});
				}
			}else{
				var stat_image_ready = 0;
				for(var k = 1; k <= nomor;k++){
					if ($('#file_'+k).length > 0) {
						stat_image_ready++;
					}
				}
				var stat_image = 0;
				for(var k = 1; k <= nomor;k++){
					if ($('#file_'+k).length > 0) {
						stat_image++;
						var fileData  = $('#file_'+k).prop('files')[0];

						file=$('#file_'+k).val().replace(/C:\\fakepath\\/i, '').split(".");

						var formData = new FormData();
						formData.append('fileData', fileData);
						formData.append('audit_id', audit_id);
						formData.append('audit_index',audit_index);
						formData.append('extension', file[1]);
						formData.append('foto_name', file[0]);
						formData.append('filename',k+'_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
						filenames.push(k+'_'+audit_index+'-'+audit_id+'{{date("YmdHisa")}}'+'.'+file[1]);
						
						$.ajax({
							url:"{{ url('upload/file/audit_ng_jelas') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								$('#loading').hide();
								filenames.push(data.filename);
								stat_image++;
							},
							error: function(data) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
							}
						});
					}
				}
				if (stat_image == stat_image_ready) {
					var formData = new FormData();
					formData.append('audit_id', audit_id);
					formData.append('audit_index', audit_index);
					formData.append('result_check', result_check);
					formData.append('note', note);
					if (filenames.length == 1) {
						formData.append('filenames', filenames[0]);
					}else{
						formData.append('filenames', filenames.join(','));
					}

					$.ajax({
						url:"{{ url('update/qa/audit_ng_jelas') }}",
						method:"POST",
						data:formData,
						dataType:'JSON',
						contentType: false,
						cache: false,
						processData: false,
						success:function(data)
						{
							if (data.status == false) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
							}else if(data.status == true){
								stat++;
							}
							$('#loading').hide();
							location.reload();
							openSuccessGritter('Success!','Update Data Success');
						},
						error: function(data) {
							$('#loading').hide();
							openErrorGritter('Error!',data.message);
							return false;
						}
					})
				}
			}
		}
	}

	function sendEmail(id,chief_foreman,manager) {
		if (confirm('Apakah Anda yakin akan mengirim Email?')) {
			$('#loading').show();
			var data = {
				id:id,
				chief_foreman:chief_foreman,
				manager:manager
			}
			$.get('{{ url("send_email/qa/audit_ng_jelas/") }}',data,  function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					location.reload();
					openSuccessGritter('Success!','Send Email Success');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}



</script>
@endsection