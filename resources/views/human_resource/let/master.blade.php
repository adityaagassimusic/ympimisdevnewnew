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
		<button class="btn btn-success pull-right btn-sm" style="margin-left: 5px; width: 10%;" onclick="$('#modalAddMaster').modal('show');cancelAll();"><i class="fa fa-plus"></i> Add Participant</button>
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
										<option value="{{$periode->fiscal_year}}">{{$periode->fiscal_year}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/human_resource/let') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/human_resource/let/master') }}" class="btn btn-danger">Clear</a>
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
							<table id="tableLet" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableLet">
									<tr>
										<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Periode</th>
										<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">ID</th>
										<th width="5%" style="background-color: rgb(126,86,134); color: #fff;">Name</th>
										<th width="5%" style="background-color: rgb(126,86,134); color: #fff;">Title</th>
										<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Dept</th>
										<th width="3%" style="background-color: rgb(126,86,134); color: #fff;">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableLet">
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

	<div class="modal fade" id="modalEditMaster">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Update LET Participant</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Periode<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<input type="hidden" class="form-control" id="id" name="id" placeholder="id">
								<select class="form-control" id="edit_periode" style="width: 100%" data-placeholder="Edit Periode">
									<option value=""></option>
									@foreach($periode3 as $periode)
									<option value="{{$periode->fiscal_year}}">{{$periode->fiscal_year}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Title<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<input type="text" name="edit_title" id="edit_title" placeholder="Edit Title" class="form-control" style="width: 100%">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Peserta<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<select class="form-control" id="edit_employee" style="width: 100%" data-placeholder="Edit Employee">
									<option value=""></option>
									@foreach($emp as $emp)
									<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="updateMaster()" class="btn btn-success pull-right"><i class="fa fa-edit"></i> Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddMaster">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" style="margin-bottom: 20px">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Add LET Participant</h3>
					</center>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Periode<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<select class="form-control" id="add_periode" style="width: 100%" data-placeholder="Add Periode">
									<option value=""></option>
									@foreach($periode2 as $periode)
									<option value="{{$periode->fiscal_year}}">{{$periode->fiscal_year}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Title<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<input type="text" name="add_title" id="add_title" placeholder="Add Title" class="form-control" style="width: 100%">
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label for="" class="col-sm-2 control-label">Peserta<span class="text-red"> *</span></label>
							<div class="col-sm-10" align="left">
								<select class="form-control" id="add_employee" style="width: 100%" data-placeholder="Add Employee">
									<option value=""></option>
									@foreach($emp2 as $emp)
									<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 10px;">
					<div class="col-xs-12">
						<div class="row">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
							<button onclick="addMaster()" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
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

	jQuery(document).ready(function() {
		cancelAll();
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
		$('#edit_periode').select2({
			allowClear:true,
			dropDownParent:$('#modalEditMaster')
		});

		$('#edit_employee').select2({
			allowClear:true,
			dropDownParent:$('#modalEditMaster')
		});

		$('#add_periode').select2({
			allowClear:true,
			dropDownParent:$('#modalAddMaster')
		});

		$('#add_employee').select2({
			allowClear:true,
			dropDownParent:$('#modalAddMaster')
		});
	});

	function cancelAll() {
		$('#add_periode').val('').trigger('change');
		$('#add_employee').val('').trigger('change');
		$('#edit_periode').val('').trigger('change');
		$('#edit_employee').val('').trigger('change');
		$('#add_title').val('');
		$('#edit_title').val('');
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
			periode:$('#periode').val()
		}
		$.get('{{ url("fetch/human_resource/let/master") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableLet').DataTable().clear();
				$('#tableLet').DataTable().destroy();
				$('#bodyTableLet').html("");

				var tableDataBody = "";
				var index = 1;

				$.each(result.master, function(key, value) {
					tableDataBody += '<tr>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.periode +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.employee_id +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.name +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ (value.title || '') +'</td>';
					var dept = '';
					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.employee_id) {
							dept = result.emp[i].department_shortname;
						}
					}
					tableDataBody += '<td style="padding:10px;text-align:left">'+ dept +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:center"><button onclick="editMaster(\''+value.id+'\',\''+value.periode+'\',\''+value.employee_id+'\',\''+value.name+'\',\''+value.title+'\')" class="btn btn-warning btn-sm">Edit</button><button style="margin-left:10px" class="btn btn-danger btn-sm" onclick="deleteMaster(\''+value.id+'\')">Delete</button></td>';
					tableDataBody += '</tr>';
				})
				$('#bodyTableLet').append(tableDataBody);

				var table = $('#tableLet').DataTable({
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
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function editMaster(id,periode,employee_id,name,title) {
		cancelAll();
		$('#id').val(id);
		$('#edit_periode').val(periode).trigger('change');
		$('#edit_employee').val(employee_id+'_'+name).trigger('change');
		$('#edit_title').val(title);
		$('#modalEditMaster').modal('show');
	}

	function updateMaster() {
		$('#loading').show();
		var id = $('#id').val();
		var periode = $('#edit_periode').val();
		var employee = $('#edit_employee').val();
		var title = $('#edit_title').val();
		
		var data = {
			id:id,
			periode:periode,
			employee:employee,
			title:title,
		}

		$.post('{{ url("update/human_resource/let/master") }}',data, function(result, status, xhr){
			if(result.status){
				fillList();
				openSuccessGritter('Success!','Update Succeeded');
				$('#modalEditMaster').modal('hide');
				$('#loading').hide();
				cancelAll();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function addMaster() {
		$('#loading').show();
		var periode = $('#add_periode').val();
		var employee = $('#add_employee').val();
		var title = $('#add_title').val();
		
		var data = {
			periode:periode,
			employee:employee,
			title:title,
		}

		$.post('{{ url("input/human_resource/let/master") }}',data, function(result, status, xhr){
			if(result.status){
				fillList();
				openSuccessGritter('Success!','Input Succeeded');
				$('#modalAddMaster').modal('hide');
				$('#loading').hide();
				cancelAll();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function deleteMaster(id) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("delete/human_resource/let/master") }}',data, function(result, status, xhr){
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