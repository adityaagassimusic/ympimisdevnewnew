@extends('layouts.master')
@section('stylesheets')
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
		Schedule {{ $activity_name }} - {{ $leader }}
		<a class="btn btn-info pull-right" style="margin-left: 5px" href="{{url('index/audit_report_activity/index/'.$id)}}">
			Lakukan Audit
		</a>
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
			Buat Schedule
		</button>
		<!-- <button type="button" class="btn btn-success pull-right" style="margin-right: 5px;" data-toggle="modal" data-target="#upload-modal">
			<i class="fa fa-file-excel-o"></i> Upload Schedule
		</button> -->
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
						<div class="box-header" style="padding: 0">
							<h3 class="box-title">Filter Schedule {{ $activity_name }}</h3>
						</div>
						<form role="form" method="post" action="{{url('index/audit_guidance/filter_guidance/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group">
									<label>Bulan</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="month" autocomplete="off" placeholder="Pilih Bulan">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group pull-right">
									<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/audit_guidance/index/'.$id) }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
				  <div class="row">
				    <div class="col-xs-12" style="overflow-x: scroll;">
			          <table id="example1" class="table table-bordered table-striped table-hover">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			              	<th>Nomor Dokumen</th>
			                <th>Nama Dokumen</th>
			                <th>Bulan</th>
			                <th>Periode</th>
			                <th>Status</th>
			                <th>Kirim Email</th>
			                <th>Foreman</th>
			                <th>Manager</th>
			                <th>Action</th>
			              </tr>
			            </thead>
			            <tbody>
			              @if(count($audit_guidance) != 0)
			              @foreach($audit_guidance as $audit_guidance)
			              <tr>
			              	<td>{{$audit_guidance->no_dokumen}}</td>
			                <td>{{$audit_guidance->nama_dokumen}}</td>
			                <td>{{$monthTitle = date("F Y", strtotime($audit_guidance->month))}}</td>
			                <td>{{$audit_guidance->periode}}</td>
			                <td>@if($audit_guidance->status == "Belum Dikerjakan")
			                		<label class="label label-danger">Belum Dikerjakan</label>
			                	@else
			                		<label class="label label-success">Sudah Dikerjakan</label>
			                	@endif
			        		</td>
			        		<td>
			        			@if($audit_guidance->send_status == null)
			                		<button class="btn btn-success btn-xs" onclick="sendEmail('{{$id}}','{{$audit_guidance->periode}}')">Kirim Email</button>
			                	@endif
			        		</td>

			        		@if($audit_guidance->approval_foreman == null)
			        		<td style="background-color: #ffa1a1;font-size: 11px;">Foreman<br>Waiting</td>
			                @else
			                <td style="background-color: #a4ff9c;font-size: 11px;">Approved<br>{{explode('_',$audit_guidance->approval_foreman)[1]}}<br>{{explode('_',$audit_guidance->approval_foreman)[2]}}</td>
			                @endif

			                @if($audit_guidance->approval_manager == null)
			        		<td style="background-color: #ffa1a1;font-size: 11px;">Manager<br>Waiting</td>
			                @else
			                <td style="background-color: #a4ff9c;font-size: 11px;">Approved<br>{{explode('_',$audit_guidance->approval_manager)[1]}}<br>{{explode('_',$audit_guidance->approval_manager)[2]}}</td>
			                @endif
			                <td>
			                  <center>
								<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit('{{ url("index/audit_guidance/update") }}','{{ $id }}','{{ $audit_guidance->id }}');">
					               Edit
					            </button>
			                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/audit_guidance/destroy") }}', '{{ $audit_guidance->nama_dokumen }} - {{ $audit_guidance->no_dokumen }} - {{ $audit_guidance->month }}','{{ $id }}', '{{ $audit_guidance->id }}');">
			                      Delete
			                    </a>
			                  </center>
			                </td>
			              </tr>
			              @endforeach
			              @endif
			            </tbody>
			          </table>
				    </div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body-delete">
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>

 <div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: orange">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Create Schecule Audit</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" method="post" action="{{url('index/audit_guidance/store/'.$id)}}" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Masukkan Leader" value="{{ $id }}" readonly>
	            <div class="form-group" id="divAddDokumen">
	              <label for="" class="col-xs-12" style="padding: 0">Dokumen IK</label>
	              <select class="form-control" data-placeholder="Pilih Dokumen IK" name="inputnama_dokumen" id="inputnama_dokumen" style="width: 100%">
	              	<option value=""></option>
	              	@foreach($documents as $documents)
	              	<option value="{{$documents->document_number}}">{{$documents->document_number}} - {{$documents->title}}</option>
	              	@endforeach
	              </select>
				  <!-- <input type="text" class="form-control" name="inputnama_dokumen" id="inputnama_dokumen" placeholder="Masukkan Nama Dokumen" required> -->
	            </div>
	            <div class="form-group">
	              <label for="">Bulan</label>
	              <div class="input-group date">
					  <div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					  </div>
					  <input type="text" class="form-control pull-right" id="inputmonth" name="inputmonth" autocomplete="off" placeholder="Pilih Bulan" required>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Periode</label>
	              <input type="text" class="form-control" name="inputperiode" id="inputperiode" placeholder="Masukkan No. Dokumen" value="{{ $fy }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="inputleader" id="inputleader" placeholder="Masukkan Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="inputforeman" id="inputforeman" placeholder="Masukkan Leader" value="{{ $foreman }}" readonly>
	            </div>
            </div>
          <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> -->
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Submit" class="btn btn-success">
          </div>
          <!-- </div> -->
        </form>
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
        <h4 class="modal-title" align="center"><b>Update Schecule Audit</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" id="formedit" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Masukkan Leader" value="{{ $id }}" readonly>
	            <div class="form-group" id="divEditDokumen">
	              <label for="" class="col-xs-12" style="padding: 0">Dokumen IK</label>
	              <select class="form-control" data-placeholder="Pilih Dokumen IK" name="editnama_dokumen" id="editnama_dokumen" style="width: 100%">
	              	<option value=""></option>
	              	@foreach($documents2 as $documents2)
	              	<option value="{{$documents2->document_number}}">{{$documents2->document_number}} - {{$documents2->title}}</option>
	              	@endforeach
	              </select>
				  <!-- <input type="text" class="form-control" name="editnama_dokumen" id="editnama_dokumen" placeholder="Masukkan Nama Dokumen" required> -->
	            </div>
	            <!-- <div class="form-group">
	              <label for="">No. Dokumen</label>
				  <input type="text" class="form-control" name="editno_dokumen" id="editno_dokumen" placeholder="Masukkan No. Dokumen" required>
	            </div> -->
	            <div class="form-group">
	              <label for="">Bulan</label>
	              <div class="input-group date">
					  <div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					  </div>
					  <input type="text" class="form-control pull-right" id="editmonth" name="editmonth" autocomplete="off" placeholder="Pilih Bulan" required>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Periode</label>
	              <input type="text" class="form-control" name="editperiode" id="editperiode" placeholder="Masukkan No. Dokumen" value="{{ $fy }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="editleader" id="editleader" placeholder="Masukkan Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="editforeman" id="editforeman" placeholder="Masukkan Leader" value="{{ $foreman }}" readonly>
	            </div>
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

<div class="modal fade" id="upload-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" style="background-color: lightskyblue">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Upload Schecule Audit</b></h4>
      </div>
      <div class="modal-body">
      	<div class="col-xs-12" style="margin-bottom: 20px;padding-left: 0px;">
      		<a class="btn btn-info" href="{{url('fetch/audit_guidance/template')}}">Template Excel</a>
      	</div>
      	<div class="col-xs-12" style="padding-left: 0px;">
			<div class="form-group row" align="right">
				<label for="" class="col-sm-2 control-label">File Excel<span class="text-red"> :</span></label>
				<div class="col-sm-8" align="left">
					<input type="file" name="fileExcel" id="fileExcel">
				</div>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button class="btn btn-success" onclick="uploadSchedule()">Upload</button>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#date').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#inputmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#editmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		
		$('#inputnama_dokumen').select2({
			allowClear:true,
			dropdownParent: $('#divAddDokumen'),
		});
		$('#editnama_dokumen').select2({
			allowClear:true,
			dropdownParent: $('#divEditDokumen'),
		});
	});

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
      var table = $('#example1').DataTable({
        "order": [],
        'dom': 'Bfrtip',
        'responsive': true,
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
          },
          ]
        }
      });
    });
    function deleteConfirmation(url, name,id,audit_guidance_id) {
      jQuery('.modal-body-delete').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+audit_guidance_id);
    }

    function sendEmail(id,periode) {
    	$('#loading').show();
    	var data = {
    		periode:periode,
    		id:id
    	}
    	$.get('{{ url("send/audit_guidance/email") }}',data, function(result, status, xhr){
			if(result.status){
				alert('Success Send Email');
				$('#loading').hide();
				location.reload();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
    }

    function edit(url, id,audit_guidance_id) {
    	$.ajax({
                url: "{{ route('audit_guidance.getdetail') }}?id=" + audit_guidance_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  // console.log(data);
                  $("#editnama_dokumen").val(data.no_dokumen).trigger('change');
                  // $("#editno_dokumen").val(data.no_dokumen);
                  $("#editmonth").val(data.month);
                }
            });
      jQuery('#formedit').attr("action", url+'/'+id+'/'+audit_guidance_id);
    }

    function uploadSchedule() {
    	$('#loading').show();
		if($('#menuDate').val() == ""){
			openErrorGritter('Error!', 'Please input period');
			audio_error.play();
			$('#loading').hide();
			return false;	
		}

		var formData = new FormData();
		var newAttachment  = $('#fileExcel').prop('files')[0];
		var file = $('#fileExcel').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);
		formData.append('activity_list_id', '{{$id}}');

		$.ajax({
			url:"{{ url('input/audit_guidance/template') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					alert('Success!');
					$('#fileExcel').val("");
					$('#upload-modal').modal('hide');
					$('#loading').hide();
					location.reload();
				}else{
					alert(data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
    }
  </script>
@endsection