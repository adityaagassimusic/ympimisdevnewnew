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
			<button data-toggle="modal" data-target="#modalAdd" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i>&nbsp; Add New Base Flow</button>
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
								<th style="width: 5%;">Flow Name</th>
								<th style="width: 3%;">Category</th>
								<th style="width: 1%;">Created_at</th>
								<th style="width: 1%;">Action</th>
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
					<h4 class="modal-title"><center>Add New Base Flow</center></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="form-group">
									<label>Base Flow Name<span class="text-red">*</span></label>
									<input type="text" style="width: 100%" class="form-control" id="flow_name" placeholder="Input Base Flow Name">
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Category<span class="text-red">*</span></label>
									<select class="form-control select2" id="category" data-placeholder='Select Category' style="width: 100%">
										<option value="">&nbsp;</option>
										<option value="Jig">Jig</option>
										<option value="Molding">Molding</option>
										<option value="Equipment">Equipment</option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-2">
										PROC NO.
									</div>
									<div class="col-xs-7">
										PROCESS NAME
									</div>
									<div class="col-xs-2">
										DURATION (min)
									</div>
									<div class="col-xs-1">
										<button class="btn btn-primary btn-sm pull-right" onclick="add_process()"><i class="fa fa-plus"></i>&nbsp; Add</button>
									</div>
								</div>
								<div class="col-xs-12" id="div_process">
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
	
	var proc_list = <?php echo json_encode($proc_list); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillMasterTable();

		$('.select2').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true,
		});
	});

	function fillMasterTable(){
		var data = {

		}

		$.get('{{ url("fetch/workshop/flow") }}', data, function(result, status, xhr){
			$("#masterBody").empty();
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			var body = "";
			no = 1;
			$.each(result.flow_list, function(index, value){
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.flow_name+"</td>";
				body += "<td>"+value.category+"</td>";
				body += "<td>"+value.created_at+"</td>";
				body += "<td><button class='btn btn-primary btn-xs' onclick='openEdit(\""+value.flow_name+"\",\""+value.category+"\" )'><i class='fa fa-pencil'></i>&nbsp; Edit</button><button class='btn btn-danger btn-xs' onclick='openDelete(\""+value.flow_name+"\",\""+value.category+"\" )'><i class='fa fa-trash'></i>&nbsp; Delete</button></td>";
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

	function add_process() {
		var proc = "";
		proc += '<tr><td><div class="col-xs-2" style="margin-top: 5px">';
		proc += '<input type="text" class="form-control" id="proses_no" readonly>';
		proc += '</div>';
		proc += '<div class="col-xs-7" style="margin-top: 5px">';
		proc += '<select class="form-control select2 id_proc" id="flow_proses" data-placeholder="Select Process">';
		proc += '<option value="">&nbsp;</option>';
		$.each(proc_list, function(index, value){
			proc += '<option value='+value.machine_code+'>'+value.process_name+' - '+value.machine_name+' - '+value.area_name+'</option>';
		})
		proc += '</select>';
		proc += '</div>';
		proc += '<div class="col-xs-2" style="margin-top: 5px">';
		proc += '<input type="text" class="form-control duration" id="duration" placeholder="Input Duration">';
		proc += '</div>';
		proc += '<div class="col-xs-1" style="margin-top: 5px">';
		proc += '<button class="btn btn-danger btn-xs" onclick="deleteFlow(this)"><i class="fa fa-close"></i></button>';
		proc += '</div>';
		proc += '</td></tr>';

		$("#div_process").append(proc);

		$('.select2').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true,
		});
	}

	function save_data() {

		if ($("#flow_name").val() == "" || $("#category").val() == "") {
			openErrorGritter("Save Failed", "Please fill All field");
			return false;
		}

		if ($('.id_proc').length == 0) {
			openErrorGritter("Save Failed", "Please Add Process");
			return false;	
		}

		var proc_arr = [];
		var dur_arr = [];

		$('.id_proc').each(function() {
			proc_arr.push($(this).val());
		});

		$('.duration').each(function() {
			dur_arr.push($(this).val());
		});
		
		var data = {
			flow_name : $("#flow_name").val(),
			category : $("#category").val(),
			process : proc_arr,
			duration : dur_arr
		}

		$.post('{{ url("post/workshop/flow") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', 'Flow Data Has Been Saved');
				$("#flow_name").val("");
				$("#category").val("");
				$("#div_process").empty();
				$("#modalAdd").modal('hide');
				fillMasterTable();
			}
		})
	}

	function openEdit(flow_name, category) {
		var data = {
			flow_name : flow_name,
			category : category
		}

		$("#div_process").empty();


		$.get('{{ url("fetch/workshop/flow/by_flow") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#modalAdd").modal('show');

				$.each(result.flows, function(index, value){
					$("#flow_name").val(value.flow_name);
					$("#category").val(value.category).trigger('change');

					var proc = "";
					proc += '<div class="col-xs-2" style="margin-top: 5px">';
					proc += '<input type="text" class="form-control" id="proses_no" readonly>';
					proc += '</div>';
					proc += '<div class="col-xs-7" style="margin-top: 5px">';
					proc += '<select class="form-control select2 id_proc" id="flow_proses" data-placeholder="Select Process" style="width:100%">';
					proc += '<option value="">&nbsp;</option>';
					$.each(proc_list, function(index, value2){
						var selected = "";
						if (value2.machine_code == value.flow_process) {
							selected = "selected";
						}

						proc += '<option '+selected+' value='+value2.machine_code+' >'+value2.process_name+' - '+value2.machine_name+' - '+value2.area_name+'</option>';
					})
					proc += '</select>';
					proc += '</div>';
					proc += '<div class="col-xs-2" style="margin-top: 5px">';
					proc += '<input type="text" class="form-control duration" id="duration" placeholder="Input Duration" value="'+value.duration+'">';
					proc += '</div>';
					proc += '<div class="col-xs-1" style="margin-top: 5px">';
					proc += '<button class="btn btn-danger btn-xs"><i class="fa fa-close"></i></button>';
					proc += '</div>';

					$("#div_process").append(proc);
				})

				$('.select2').select2({
					dropdownParent: $('#modalAdd'),
					allowClear: true,
				});
			}
		})
	}

	function openDelete(flow_name, category) {
		if (confirm('Are you sure want to delete this flow process "'+flow_name+'"?')) {
			var data = {
				flow_name : flow_name,
				category : category
			}

			$.get('{{ url("delete/workshop/flow") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'Flow Data Has Been Deleted');

					fillMasterTable();
				}
			})
		}
	}

	function deleteFlow(elem) {
		$(elem).closest('tr').remove();
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