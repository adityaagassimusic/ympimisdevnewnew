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
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	th:hover {
		overflow: visible;
	}
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
		<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#modalUploadTeam').modal('show')"><i class="fa fa-upload"></i> Upload Team</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="periode" id="periode" data-placeholder="Pilih Periode" style="width: 100%;">
									<option></option>
									@foreach($periode as $periode)
										<option value="{{$periode->periode}}">{{$periode->periode}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/sga') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/sga/master') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableSga" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableSga">
									<tr>
										<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Periode</th>
										<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Team No.</th>
										<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Team Name</th>
										<th width="6%" style="background-color: rgb(126,86,134); color: #fff;">Title</th>
										<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Day</th>
										<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableSga">
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

	<div class="modal fade" id="modalUploadTeam">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload SGA Team</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="periode_upload" name="periode_upload" placeholder="Masukkan Periode (Contoh : Q3_FY198)">
								<span style="color: red">Contoh : <b>Q3_FY198</b></span>
							</div>
							<div class="col-sm-4" align="left">
								<a class="btn btn-info pull-right" href="{{url('download/sga/master')}}">Example</a>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
							<div class="col-sm-8" align="left">
								<input type="file" name="teamFile" id="teamFile">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="uploadTeam()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalUpdateTeam">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Update SGA Team</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">Team No.<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="hidden" class="form-control" id="id" name="id" placeholder="id">
								<input type="text" class="form-control" id="team_no" name="team_no" placeholder="Team No.">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">Team Name<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="team_name" name="team_name" placeholder="Team Name">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">Team Title<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="team_title" name="team_title" placeholder="Team Title">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-4 control-label">Day<span class="text-red"> :</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="day" name="day" placeholder="Day">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="updateTeam()" class="btn btn-success pull-right"><i class="fa fa-edit"></i> Update</button>
						</div>
					</div>
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
	var teams_all = [];

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
		teams_all = [];
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
			periode:$('#periode').val(),
		}
		$.get('{{ url("fetch/sga/master") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableSga').DataTable().clear();
				$('#tableSga').DataTable().destroy();
				$('#bodyTableSga').html("");

				var tableDataBody = "";
				var index = 1;

				$.each(result.teams, function(key, value) {
					tableDataBody += '<tr>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.periode +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.team_no +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.team_name +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.team_title +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:right">'+ value.day +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:center"><button onclick="editTeam(\''+value.id+'\',\''+value.team_no+'\',\''+value.team_name+'\',\''+value.team_title+'\',\''+value.day+'\')" class="btn btn-warning btn-sm">Edit</button><button style="margin-left:10px" class="btn btn-danger btn-sm" onclick="deleteTeam(\''+value.id+'\')">Delete</button></td>';
					tableDataBody += '</tr>';
				})
				$('#bodyTableSga').append(tableDataBody);

				var table = $('#tableSga').DataTable({
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
					"order": [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				teams_all = [];

				teams_all = result.teams_all;
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function editTeam(id,team_no,team_name,team_title,day) {
		$('#id').val(id);
		$('#team_no').val(team_no);
		$('#team_name').val(team_name);
		$('#team_title').val(team_title);
		$('#day').val(day);
		$('#modalUpdateTeam').modal('show');
	}

	function updateTeam() {
		$('#loading').show();
		var id = $('#id').val();
		var team_no = $('#team_no').val();
		var team_name = $('#team_name').val();
		var team_title = $('#team_title').val();
		var day = $('#day').val();

		var data = {
			id:id,
			team_no:team_no,
			team_name:team_name,
			team_title:team_title,
			day:day,
		}

		$.post('{{ url("update/sga/master") }}',data, function(result, status, xhr){
			if(result.status){
				fillList();
				openSuccessGritter('Success!','Update Succeeded');
				$('#modalUpdateTeam').modal('hide');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function uploadTeam(){
		$('#loading').show();
		if($('#periode_upload').val() == ""){
			openErrorGritter('Error!', 'Please input period');
			audio_error.play();
			$('#loading').hide();
			return false;	
		}

		var formData = new FormData();
		var newAttachment  = $('#teamFile').prop('files')[0];
		var file = $('#teamFile').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('periode', $("#periode_upload").val());

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/sga/master') }}",
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
					$('#periode_upload').val("");
					$('#teamFile').val("");
					$('#modalUploadTeam').modal('hide');
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

	function deleteTeam(id) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("delete/sga/master") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					openSuccessGritter('Success!','Delete Succeeded');
					$('#loading').hide();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }



</script>
@endsection