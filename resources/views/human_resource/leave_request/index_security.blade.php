@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		<center><span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">Surat Izin Keluar Perusahaan</span></center>
		<br>
	</h1>
</section>
@stop

@section('content')
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
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
<div class="row">
<div class="col-xs-12">
	<div class="box">
		<div class="box-body">
			<div>
				<div style="text-align: center;background-color: #42117d;color: white">
					<center style="width: 100%;padding: 10px"><span style="font-weight: bold;font-size: 20px;">LIST SURAT IZIN KELUAR PERUSAHAAN</span></center>
				</div>
				<div class="col-xs-12" style="margin-top: 10px;padding-left: 0px;padding-right: 0px">
					<button class="btn btn-primary" onclick="fetchList()">
						<i class="fa fa-refresh"></i> Refresh
					</button>
					<div style="margin-top: 10px">
						<table id="leaveRequestTable" class="table table-bordered table-striped table-hover" >
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th width="1%">ID</th>
									<th width="1%">Tanggal Pengajuan</th>
									<th width="2%">Karyawan</th>
									<th width="2%">Dept</th>
									<th width="1%">Kategori</th>
									<th width="2%">Keperluan</th>
									<th width="2%">Waktu Keluar</th>
									<th width="2%">Waktu Kembali</th>
									<th width="2%">Kembali/Tidak</th>
									<th width="2%">Action</th>
								</tr>
							</thead>
							<tbody id="leaveRequestBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div class="modal modal-default fade" id="confirm-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Konfirmasi Surat Izin Keluar</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="hidden" name="operator_id" id="operator_id">
							<div class="form-group row" align="right">
								<label class="col-sm-2">Request ID<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="request_id" placeholder="Request ID" required readonly>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-2">Status<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left">
									<input type="text" class="form-control" id="position" placeholder="Position" required readonly>
								</div>
							</div>
							<div class="form-group row" align="right">
								<label class="col-sm-2">Nama Security<span class="text-red">*</span></label>
								<div class="col-sm-5" align="left" id="selectPur">
									<select class="form-control selectPur" data-placeholder="Pilih Nama Security" name="confirmed_by" id="confirmed_by" style="width: 100%">
										<option value=""></option>
										@foreach($security as $security)
										<option value="{{$security->employee_id}}_{{$security->name}}">{{$security->employee_id}} - {{$security->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<table id="leaveRequestTableDetail" class="table table-bordered table-striped table-hover" style="margin-top: 20px">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th width="1%">No.</th>
										<th width="1%">NIK</th>
										<th width="3%">Nama</th>
										<th width="1%">Dept</th>
										<th width="2%">Keluar</th>
										<th width="2%">Kembali</th>
									</tr>
								</thead>
								<tbody id="leaveRequestBodyDetail">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 25px" onclick="updateConfirmation()"><i class="fa fa-check-square-o"></i> KONFIRMASI</button>
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
<script >
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() { 
		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});

		fetchList();
		$('.selectPur').select2({
			dropdownParent: $('#selectPur'),
			allowClear:true
		});

	});


	function fetchList() {
		$.get('{{ url("fetch/human_resource/leave_request/security") }}', function(result, status, xhr){
			if(result.status){
				$('#leaveRequestTable').DataTable().clear();
				$('#leaveRequestTable').DataTable().destroy();
				$('#leaveRequestBody').html("");
				var tableData = "";
				$.each(result.leave_request, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="color:red;font-weight:bold">'+ value.request_id +'</td>';
					tableData += '<td>'+ value.date +'</td>';
					tableData += '<td>';
					var karyawan = [];
					for(var i = 0; i < result.leave_details.length;i++){
						if (result.leave_details[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_details[i].length;j++){
								karyawan.push(result.leave_details[i][j].employee_id+' - '+result.leave_details[i][j].name);
							}
						}
					}
					tableData += karyawan.join('<br>');
					tableData += '</td>';
					tableData += '<td>'+ value.department_shortname +'</td>';
					tableData += '<td>'+ value.purpose_category +'</td>';
					tableData += '<td>'+ value.purpose_detail +'</td>';
					tableData += '<td>'+ value.time_departure +'</td>';
					tableData += '<td>'+ value.time_arrived +'</td>';
					if (value.return_or_not == 'YES') {
						tableData += '<td>KEMBALI</td>';
					}else{
						tableData += '<td>TIDAK KEMBALI</td>';
					}
					tableData += '<td>';
					if (value.position == 'security') {
						var position = 'Keluar';
						tableData += '<a style="margin-right:2px" type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#confirm-modal" onclick="confirmRequest(\''+value.request_id+'\',\''+position+'\');">Keluar</a>';
					}else if (value.position == 'out') {
						var position = 'Kembali';
						tableData += '<a style="margin-right:2px" type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-modal" onclick="confirmRequest(\''+value.request_id+'\',\''+position+'\');">Kembali</a>';
					}{
						tableData += '';
					}
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#leaveRequestBody').append(tableData);

				var table = $('#leaveRequestTable').DataTable({
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

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function confirmRequest(request_id,position) {

		var data = {
			request_id:request_id,
			position
		}
		$('#confirmed_by').val('').trigger('change');
		$('#request_id').val(request_id);
		$('#position').val(position);

		$.get('{{ url("fetch/human_resource/leave_request/security/detail") }}',data, function(result, status, xhr){
			if(result.status){
				var body_details = '';
				$('#leaveRequestBodyDetail').html('');
				var index = 1;

				for(var i = 0; i < result.leave_details.length;i++){
					body_details += '<tr>';
					body_details += '<td>'+index+'</td>';
					body_details += '<td>'+result.leave_details[i].employee_id+'</td>';
					body_details += '<td>'+result.leave_details[i].name+'</td>';
					body_details += '<td>'+result.leave_details[i].department_shortname+'</td>';
					if (result.leave_details[i].confirmed_at == null) {
						body_details += '<td style="padding:0px"><input class="input_tag" type="text" id="tag_'+result.leave_details[i].employee_id+'_'+result.leave_details[i].request_id+'" style="width:100%;height:40px" onkeyup="scanTagConfirmation(this.id,)" placeholder="Scan ID Card Disini"></td>';
					}else{
						body_details += '<td>'+result.leave_details[i].confirmed_at+'</td>';
					}
					if (result.leave_details[i].returned_at == null) {
						body_details += '<td style="padding:0px"><input class="input_tag" type="text" id="tag_'+result.leave_details[i].employee_id+'_'+result.leave_details[i].request_id+'" style="width:100%;height:40px" onkeyup="scanTagConfirmation(this.id,)" placeholder="Scan ID Card Disini"></td>';
					}else{
						body_details += '<td>'+result.leave_details[i].returned_at+'</td>';
					}
					body_details += '</tr>';
					index++;
				}

				$('#leaveRequestBodyDetail').append(body_details);

				$('#tag_'+result.leave_details[0].employee_id+'_'+result.leave_details[0].request_id).focus();
			}else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function scanTagConfirmation(id) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#'+id).val().length == 10 || $('#'+id).val().includes('PI')|| $('#'+id).val().includes('OS')) {
				var request_id = id.split('_')[2];
				var employee_id = id.split('_')[1];
				var data = {
					tag:$('#'+id).val(),
					request_id:request_id,
					employee_id:employee_id,
					position:$('#position').val()
				}
				$.get('{{ url("scan/human_resource/leave_request/security") }}',data, function(result, status, xhr){
					if(result.status){
						confirmRequest(result.request_id,$('#position').val());
						openSuccessGritter('Success!',result.message);
					}
					else{
						$('#'+id).val('');
						$('#'+id).focus();
						openErrorGritter('Error!',result.message);
					}
				});
			}else{
				$('#'+id).val('');
				$('#'+id).focus();
				openErrorGritter('Error!','Tag Invalid');
			}
		}
	}

	function updateConfirmation() {
		$('#loading').show();
		if ($('#confirmed_by').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Nama Security');
			return false;
		}
		if (document.getElementsByClassName("input_tag").length > 0 && $('#position').val() == 'Kembali' && document.getElementsByClassName("input_tag")[0].value == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Scan ID Card / Ketik NIK');
			return false;
		}
		var data = {
			request_id:$('#request_id').val(),
			employee_id:$('#confirmed_by').val().split('_')[0],
			name:$('#confirmed_by').val().split('_')[1],
			position:$('#position').val(),
		}

		$.get('{{ url("confirm/human_resource/leave_request/security") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!',result.message);
				fetchList();
				$('#loading').hide();
				$('#confirm-modal').modal('hide');
			}else{
				$('#loading').hide();
				openSuccessGritter('Error!',result.message);
			}
		});
	}


	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
@endsection