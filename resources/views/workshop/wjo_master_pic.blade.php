@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> </span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button data-toggle="modal" data-target="#modalAdd" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i>&nbsp; Add New PIC Master</button>
		</li>
	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 10%;">Process Name</th>
								<th style="width: 3%;">Number of Employee</th>
								<th style="width: 3%;">Action</th>
							</tr>
						</thead>
						<tbody id="masterBody">
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modalAdd">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><center>Add New PIC Process</center></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12">
								<div class="form-group">
									<label>Process Name<span class="text-red">*</span></label>
									<select class="form-control select2" id="proc_name" data-placeholder="Input Process Name" style="width: 100%">
										<option value="">&nbsp;</option>
										<?php foreach ($processes as $proc) {
											echo '<option value="'.$proc->machine_code.'">'.$proc->process_name.' - '.$proc->machine_name.' - '.$proc->area_name.'</option>';
										} ?>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-5">
										WORKSHOP OPERATOR
									</div>
									<div class="col-xs-3">
										WORKGROUP
									</div>
									<div class="col-xs-2">
										SKILL
									</div>
									<div class="col-xs-2">
										<button class="btn btn-primary btn-sm pull-right" onclick="add_employee()"><i class="fa fa-plus"></i>&nbsp; Add</button>
									</div>
								</div>
								<div class="col-xs-12" id="div_employee">
								</div>
								<div class="col-xs-12">
									<button class="btn btn-success btn-sm pull-right" onclick="save_data()"><i class="fa fa-check"></i>&nbsp; Save</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	var emp_list = <?php echo json_encode($emp_list); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillMasterTable();

		$('.select2').select2({
			dropdownPosition: 'below',
			allowClear: true,
		});
	});

	function fillMasterTable(){
		var data = {

		}

		$.get('{{ url("fetch/workshop/pic") }}', data, function(result, status, xhr){
			$("#masterBody").empty();
			var body = "";
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			no = 1;
			$.each(result.pic_list, function(index, value){
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.process_name+" - "+value.machine_name+" - "+value.area_name+"</td>";
				body += "<td>"+value.jml_op+"</td>";

				var proc_name = value.process_name+" - "+value.machine_name+" - "+value.area_name;

				body += "<td><button class='btn btn-primary btn-xs' onclick='openEdit(\""+value.process_id+"\" )'><i class='fa fa-pencil'></i>&nbsp; Edit</button><button class='btn btn-danger btn-xs' onclick='openDelete(\""+value.process_id+"\", \""+proc_name+"\" )'><i class='fa fa-trash'></i>&nbsp; Delete</button></td>";
				body += "</tr>";
				no++;
			});
			$("#masterBody").append(body);

			$('#masterTable').DataTable({
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
					]
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
				'searching': true,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
		})

	}

	function save_data() {

		if ($("#proc_name").val() == "") {
			openErrorGritter("Save Failed", "Please fill All field");
			return false;
		}

		if ($('.id_emp').length == 0) {
			openErrorGritter("Save Failed", "Please Add Employee");
			return false;	
		}

		if ($('.skill').length == 0) {
			openErrorGritter("Save Failed", "Please Add Skill");
			return false;	
		}

		var emp_arr = [];

		$('.id_emp').each(function() {
			emp_arr.push($(this).val());
		});

		var group_arr = [];

		$('.work_group').each(function() {
			group_arr.push($(this).val());
		});

		var skill_arr = [];

		$('.skill').each(function() {
			skill_arr.push($(this).val());
		});
		
		var data = {
			proc_name : $("#proc_name").val(),
			employee : emp_arr,
			group : group_arr,
			skill : skill_arr,
		}

		$.post('{{ url("post/workshop/pic") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', 'PIC Data Has Been Saved');
				$("#proc_name").val("");
				$("#div_employee").empty();
				$("#modalAdd").modal('hide');
				fillMasterTable();
			}
		})
	}

	function add_employee() {
		var emp = "";
		emp += '<tr><td>';
		emp += '<div class="col-xs-5" style="margin-top: 5px">';
		emp += '<select class="form-control select2 id_emp" data-placeholder="Select Employee" style="width:100%">';
		emp += '<option value="">&nbsp;</option>';
		$.each(emp_list, function(index, value){
			emp += '<option value="'+value.employee_id+'">'+value.employee_id+' - '+value.name+'</option>';
		})
		emp += '</select>';
		emp += '</div>';
		emp += '<div class="col-xs-3 wrk" style="margin-top: 5px">';
		emp += '<input type="text" class="form-control work_group" id="work_group" >';
		emp += '</div>';
		emp += '<div class="col-xs-2 skl" style="margin-top: 5px">';
		emp += '<input type="text" class="form-control skill">';
		emp += '</div>';
		emp += '<div class="col-xs-2" style="margin-top: 5px">';
		emp += '<button class="btn btn-danger btn-xs" onclick="deleteEmp(this)"><i class="fa fa-close"></i></button>';
		emp += '</div>';
		emp += '</td></tr>';

		$("#div_employee").append(emp);

		$('.select2').select2({
			dropdownPosition: 'below',
			allowClear: true,
		});
	}

	function openEdit(proc_name) {
		var data = {
			proc_name : proc_name
		}

		$("#div_employee").empty();

		$.get('{{ url("fetch/workshop/pic/by_proc") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#modalAdd").modal('show');

				$.each(result.proc, function(index, value){
					$("#proc_name").val(value.process_id).trigger('change');

					var emp = "";
					var skill = 0;
					emp += '<tr><td>';
					emp += '<div class="col-xs-5" style="margin-top: 5px">';
					emp += '<select class="form-control select2 id_emp" id="flow_emp" data-placeholder="Select Employee"  style="width:100%">';
					emp += '<option value="">&nbsp;</option>';
					$.each(emp_list, function(index, value2){
						var selected = "";
						if (value.operator_id == value2.employee_id) {
							selected = "selected";
							skill = value.skill_level;
						}
						emp += '<option '+selected+' value='+value2.employee_id+'>'+value2.employee_id+' - '+value2.name+'</option>';
					})
					emp += '</select>';
					emp += '</div>';
					emp += '<div class="col-xs-3 wrk" style="margin-top: 5px">';
					emp += '<input type="text" class="form-control work_group" id="work_group" value="'+value.process_group+'" >';
					emp += '</div>';
					emp += '<div class="col-xs-2 skl" style="margin-top: 5px">';
					emp += '<input type="text" class="form-control skill" value="'+(skill || '')+'">';
					emp += '</div>';
					emp += '<div class="col-xs-2" style="margin-top: 5px">';
					emp += '<button class="btn btn-danger btn-xs" onclick="deleteEmp(this)"><i class="fa fa-close"></i></button>';
					emp += '</div>';
					emp += '</td></tr>';

					$("#div_employee").append(emp);
				})

				$('.select2').select2({
					dropdownPosition: 'below',
					allowClear: true,
				});
			}
		})
	}

	function openDelete(proc_id, proc_name) {
		if (confirm('Are you sure want to delete this flow process "'+proc_name+'"?')) {
			var data = {
				proc_name : proc_id
			}

			$.get('{{ url("delete/workshop/pic") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'PIC Data Has Been Deleted');

					fillMasterTable();
				}
			})
		}
	}

	function deleteEmp(elem) {
		$(elem).closest('tr').remove();
	}

	function change_op(elem) {
		var emp_id = $(elem).val();

		$.each(emp_list, function(index, value){
			if (value.employee_id == emp_id) {
				$(elem).parent().parent().find('.wrk').find("input").val(value.work_grup);
				// $(elem).parent().parent().find('.skl').find("input").val(value.skill_level);
			}
		})
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection