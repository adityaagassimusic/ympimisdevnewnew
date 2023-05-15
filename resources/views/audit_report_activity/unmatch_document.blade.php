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
		<!-- <button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Document
		</button> -->
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
		<div class="col-xs-12" style="padding-right: 0px;">
			<div class="box box-solid" style="margin-bottom: 5px;">
				<div class="box-header" style="text-align: center;background-color: lightgrey;">
					<h3 class="box-title" style="font-weight: bold;">Filter</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-xs-4" style="padding-right: 0px;">
							<select class="form-control select2" style="width: 100%" id="department" name="department" data-placeholder="Pilih Department">
								<option value=""></option>
							</select>
						</div>
						<div class="col-xs-4" style="padding-left: 5px;padding-right: 0px;">
							<select class="form-control select2" style="width: 100%" id="leader" name="leader" data-placeholder="Pilih Leader">
								<option value=""></option>
							</select>
						</div>
						<div class="col-xs-2" style="padding-left: 5px;">
							<button class="btn btn-success" style="font-weight: bold;" onclick="fillData()">Search</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-5" style="padding-right: 0px;">
			<div class="box box-solid">
				<div class="box-header" style="text-align: center;background-color: orange;color: white;">
					<h3 class="box-title" style="font-weight: bold;">Unmatch Audit Leader (Tidak Ada di Audit IK Leader)</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableDocument" class="table table-bordered table-striped table-hover">
								<thead style="background-color: lightgrey">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 5%">Doc. Num</th>
										<th style="width: 10%">Title</th>
										<th style="width: 2%">Dept</th>
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

		<div class="col-xs-7" style="padding-left: 5px;">
			<div class="box box-solid">
				<div class="box-header" style="text-align: center;background-color: green;color: white;">
					<h3 class="box-title" style="font-weight: bold;">Unmatch Document Number (No. Dokumen Tidak Sama dengan STD)</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableDocumentEdit" class="table table-bordered table-striped table-hover">
								<thead style="background-color: lightgrey">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 5%">Doc. Num</th>
										<th style="width: 10%">Title</th>
										<th style="width: 2%">Leader</th>
										<th style="width: 2%">Foreman</th>
										<th style="width: 2%">Periode</th>
										<th style="width: 2%">Status</th>
										<th style="width: 2%">Dept</th>
										<th style="width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableDocumentEdit">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 5px;">
			<div class="box box-solid">
				<div class="box-header" style="text-align: center;background-color: lightskyblue">
					<h3 class="box-title" style="font-weight: bold;">Resume Audit IK Leader</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableDocumentAll" class="table table-bordered table-striped table-hover">
								<thead style="background-color: lightgrey">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 5%">Doc. Num</th>
										<th style="width: 10%">Title</th>
										<th style="width: 2%">Leader</th>
										<th style="width: 2%">Foreman</th>
										<th style="width: 2%">Periode</th>
										<th style="width: 2%">Status</th>
										<th style="width: 2%">Dept</th>
									</tr>
								</thead>
								<tbody id="bodyTableDocumentAll">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: lightgreen">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Update Document IK</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" id="formedit" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Masukkan Leader" value="" readonly>
	            <div class="form-group" id="divEditDokumen">
	              <label for="" class="col-xs-12" style="padding: 0">Dokumen IK</label>
	              <br>
	              <label for="" class="col-xs-12" style="padding: 0">Before : <span id="document_before"></span></label>
	              <select class="form-control" data-placeholder="Pilih Dokumen IK" name="editnama_dokumen" id="editnama_dokumen" style="width: 100%">
	              	<option value=""></option>
	              	@foreach($document as $document)
	              	<option value="{{$document->document_number}}">{{$document->document_number}} - {{$document->title}}</option>
	              	@endforeach
	              </select>
				  <!-- <input type="text" class="form-control" name="editnama_dokumen" id="editnama_dokumen" placeholder="Masukkan Nama Dokumen" required> -->
	            </div>
	            <!-- <div class="form-group">
	              <label for="">No. Dokumen</label>
				  <input type="text" class="form-control" name="editno_dokumen" id="editno_dokumen" placeholder="Masukkan No. Dokumen" required>
	            </div> -->
	            <!-- <div class="form-group">
	              <label for="">Bulan</label>
	              <div class="input-group date">
					  <div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					  </div>
					  <input type="text" class="form-control pull-right" id="editmonth" name="editmonth" autocomplete="off" placeholder="Pilih Bulan" required>
				  </div>
	            </div> -->
            </div>
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Update" class="btn btn-success">
          </div>
        </form>
        </div>
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
		fillData();
		$('.select2').select2({
	      allowClear:true
	    });

	    $('#editnama_dokumen').select2({
			allowClear:true,
			dropdownParent: $('#divEditDokumen'),
		});
	});

	function fillData(){
		$('#loading').show();
		var data = {
			leader:$('#leader').val(),
			department:$('#department').val(),
			leader:$('#leader').val(),
		}
		$.get('{{ url("fetch/audit_report_activity/unmatch") }}', data,function(result, status, xhr){
			if(result.status){
				var unmatch = [];
				var ik_leader = [];
				var ik_std = [];
				var ik_std_all = [];
				var leader = [];

				for(var i = 0; i < result.ik_std.length;i++){
					ik_std.push(result.ik_std[i].document_number);
				}

				for(var i = 0; i < result.ik_std_all.length;i++){
					ik_std_all.push(result.ik_std_all[i].document_number);
				}

				for(var i = 0; i < result.ik_leader.length;i++){
					leader.push(result.ik_leader[i].leader);
					ik_leader.push(result.ik_leader[i].no_dokumen);
				}

				for(var i = 0; i < ik_std.length;i++){
					if (!ik_leader.includes(ik_std[i])) {
						unmatch.push(ik_std[i]);
					}
				}

				var unmatch_std = [];
				for(var i = 0; i < result.ik_std.length;i++){
					for(var j = 0; j < unmatch.length;j++){
						if (unmatch[j] == result.ik_std[i].document_number) {
							var dept = result.ik_std[i].department_name;
							for(var k = 0; k < result.department.length;k++){
								if (result.department[k].department_name == result.ik_std[i].department_name) {
									dept = result.ik_std[i].department_shortname;
								}
							}
							unmatch_std.push({
								document_number:result.ik_std[i].document_number,
								title:result.ik_std[i].title,
								department:dept
							});
						}
					}
				}

				var unmatch = [];

				for(var i = 0; i < ik_leader.length;i++){
					if (!ik_std_all.includes(ik_leader[i])) {
						unmatch.push(ik_leader[i]);
					}
				}

				var unmatch_leader = [];
				for(var i = 0; i < result.ik_leader.length;i++){
					for(var j = 0; j < unmatch.length;j++){
						if (unmatch[j] == result.ik_leader[i].no_dokumen) {
							unmatch_leader.push({
								activity_list_id:result.ik_leader[i].activity_list_id,
								id:result.ik_leader[i].id,
								document_number:result.ik_leader[i].no_dokumen,
								title:result.ik_leader[i].nama_dokumen,
								leader:result.ik_leader[i].leader,
								foreman:result.ik_leader[i].foreman,
								periode:result.ik_leader[i].periode,
								status:result.ik_leader[i].status,
								department:result.ik_leader[i].department_shortname
							});
						}
					}
				}

				$('#tableDocument').DataTable().clear();
				$('#tableDocument').DataTable().destroy();
				$('#bodyTableDocument').html("");
				var tableDocument = "";
				
				var index = 1;

				$.each(unmatch_std, function(key, value) {
					tableDocument += '<tr>';
					tableDocument += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
					tableDocument += '<td style="text-align:left;padding-left:7px;">'+value.document_number+'</td>';
					tableDocument += '<td style="text-align:left;padding-left:7px;">'+value.title+'</td>';
					tableDocument += '<td style="text-align:left;padding-left:7px;">'+value.department+'</td>';
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

				$('#tableDocumentEdit').DataTable().clear();
				$('#tableDocumentEdit').DataTable().destroy();
				$('#bodyTableDocumentEdit').html("");
				var tableDocumentEdit = "";
				
				var index = 1;

				$.each(unmatch_leader, function(key, value) {
					tableDocumentEdit += '<tr id="'+value.leader+'">';
					tableDocumentEdit += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.document_number+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.title+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.leader+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.foreman+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.periode+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.status+'</td>';
					tableDocumentEdit += '<td style="text-align:left;padding-left:7px;">'+value.department+'</td>';
					tableDocumentEdit += '<td style="text-align:center">';
					tableDocumentEdit += '<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit(\''+value.activity_list_id+'\',\''+value.id+'\',\''+value.title+'\',\''+value.document_number+'\');">Edit</button>'
					tableDocumentEdit += '</td>';
					tableDocumentEdit += '</tr>';
					index++;
				});
				$('#bodyTableDocumentEdit').append(tableDocumentEdit);

				var table = $('#tableDocumentEdit').DataTable({
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

					$("#department").html('');
					var dept = '';
					dept += '<option value=""></option>';
					for(var i = 0; i < result.department.length;i++){
						dept += '<option value="'+result.department[i].department_name+'">'+result.department[i].department_shortname+'</option>';
					}
					$("#department").append(dept);

				var leader_unik = leader.filter(onlyUnique);

				$("#leader").html('');
				var leaders = '';
				leaders += '<option value=""></option>';
				for(var i = 0; i < leader_unik.length;i++){
					leaders += '<option value="'+leader_unik[i]+'">'+leader_unik[i]+'</option>';
				}
				$("#leader").append(leaders);

				$('#department').val(result.depts).trigger('change');
				$('#leader').val(result.leader).trigger('change');

				$('#tableDocumentAll').DataTable().clear();
				$('#tableDocumentAll').DataTable().destroy();
				$('#bodyTableDocumentAll').html("");
				var tableDocumentAll = "";
				
				var index = 1;

				$.each(result.ik_leader, function(key, value) {
					tableDocumentAll += '<tr id="'+value.leader+'">';
					tableDocumentAll += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.no_dokumen+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.nama_dokumen+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.leader+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.foreman+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.periode+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.status+'</td>';
					tableDocumentAll += '<td style="text-align:left;padding-left:7px;">'+value.department_shortname+'</td>';
					tableDocumentAll += '</tr>';
					index++;
				});
				$('#bodyTableDocumentAll').append(tableDocumentAll);

				var table = $('#tableDocumentAll').DataTable({
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

				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function edit(id,audit_guidance_id,title,docnum) {
    	$.ajax({
                url: "{{ route('audit_guidance.getdetail') }}?id=" + audit_guidance_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  // console.log(data);
                  $("#editnama_dokumen").val(data.no_dokumen).trigger('change');
                  // $("#editno_dokumen").val(data.no_dokumen);
                  // $("#editmonth").val(data.month);
                }
            });
    	$('#inputactivity_list_id').val(id);
    	$('#document_before').html(docnum+' - '+title);
      jQuery('#formedit').attr("action", '{{ url("index/audit_guidance/update_new") }}'+'/'+id+'/'+audit_guidance_id);
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
</script>
@endsection