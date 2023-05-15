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
			<button data-toggle="modal" data-target="#modalAdd" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i>&nbsp; Add Jig Data</button>
		</li>
	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 2%;">Jig Code</th>
								<th style="width: 5%;">Jig Name</th>
								<th style="width: 3%;">Process</th>
								<th style="width: 3%;">No Part</th>
								<th style="width: 3%;">Part Name</th>
								<th style="width: 3%;">Material</th>
								<th style="width: 1%;">Quantity</th>
								<th style="width: 1%;">Drawing</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="masterBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modalAdd">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><center>Add Jig Data</center></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label for="jig_code" class="col-sm-3 control-label">Jig Code:<span class="text-red">*</span></label>

								<div class="col-sm-8" style="margin-bottom: 5px">
									<input type="hidden" id="jig_id">
									<input type="text" class="form-control" id="jig_code" placeholder="Input Jig Code">
								</div>
							</div>

							<div class="form-group">
								<label for="jig_proses" class="col-sm-3 control-label">Process:<span class="text-red">*</span></label>

								<div class="col-sm-9" style="margin-bottom: 5px">
									<select class="form-control select2" id="jig_proses" data-placeholder="Select Process" style="width: 100%">
										<option value=""></option>
										<option value="Lacquering">Lacquering</option>
										<option value="Plating">Plating</option>
										<option value="BPRO">BPRO</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="jig_name" class="col-sm-3 control-label">Jig Name:<span class="text-red">*</span></label>

								<div class="col-sm-9" style="margin-bottom: 5px">
									<input type="text" class="form-control" id="jig_name" placeholder="Input Jig Name">
								</div>
							</div>

							<div class="form-group">
								<label for="jig_qty" class="col-sm-3 control-label">Quantity:<span class="text-red">*</span></label>

								<div class="col-sm-7" style="margin-bottom: 5px">
									<input type="text" class="form-control" id="jig_qty" placeholder="Input Quantity">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">No Part:</label>

								<div class="col-sm-7" style="margin-bottom: 5px">
									<input type="text" class="form-control" id="jig_part" placeholder="Input Part Number">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Nama Part:</label>

								<div class="col-sm-7" style="margin-bottom: 5px">
									<input type="text" class="form-control" id="jig_nama_part" placeholder="Input Part Name">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Material:</label>

								<div class="col-sm-7" style="margin-bottom: 5px">
									<input type="text" class="form-control" id="jig_mat" placeholder="Input Material">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Drawing:</label>

								<div class="col-sm-6" style="margin-bottom: 5px">
									<input type="file" name="drawing" id="drawing">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" id="btn_save" onclick="save_jig()">Save Jig</button>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillMasterTable();

		$('.select2').select2({
			dropdownParent: $('#modalAdd'),
			allowClear: true,
			tags: true
		});
	});

	function fillMasterTable(){
		var data = {

		}

		$.get('{{ url("fetch/workshop/jig") }}', data, function(result, status, xhr){
			$("#masterBody").empty();
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			var body = "";
			no = 1;
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.jig_code+"</td>";
				body += "<td>"+value.jig_name+"</td>";
				body += "<td>"+value.process+"</td>";
				console.log(value.part_number);
				body += "<td>"+(value.part_number || '')+"</td>";
				body += "<td>"+(value.part_name || '')+"</td>";
				body += "<td>"+(value.material || '')+"</td>";
				body += "<td>"+value.quantity+"</td>";

				if (value.drawing) {
					var url = "{{ url('workshop/Drawing_Jig') }}/"+value.drawing;
					body += "<td><a href='"+url+"' target='_blank'>"+value.drawing+"</a></td>";
				} else {
					body += "<td></td>";
				}

				body += "<td><button class='btn btn-primary btn-xs' onclick='openEdit(\""+value.id+"\" )'><i class='fa fa-pencil'></i>&nbsp; Edit</button><button class='btn btn-danger btn-xs' onclick='openDelete(\""+value.id+"\", \""+value.jig_code+"\" )'><i class='fa fa-trash'></i>&nbsp; Delete</button></td>";
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


	function save_jig() {
		$("#loading").show();

		var formData = new FormData();

		formData.append('jig_id', $("#jig_id").val());
		formData.append('jig_code', $("#jig_code").val());
		formData.append('jig_name', $("#jig_name").val());
		formData.append('jig_proses', $("#jig_proses").val());
		formData.append('jig_qty', $("#jig_qty").val());
		formData.append('jig_part', $("#jig_part").val());
		formData.append('jig_nama_part', $("#jig_nama_part").val());
		formData.append('jig_mat', $("#jig_mat").val());
		formData.append('drawing', $("#drawing").prop('files')[0]);

		$.ajax({
			url:"{{ url('post/workshop/jig') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				if (response.status) {
					$("#loading").hide();
					openSuccessGritter('Success', 'Jig Data Has Been Saved');
					$("#jig_id").val("");
					$("#jig_code").val("");
					$("#jig_name").val("");
					$("#jig_part").val("");
					$("#jig_nama_part").val("");
					$("#jig_mat").val("");
					$("#jig_proses").val("").trigger('change');
					$("#jig_qty").val("");
					$("#modalAdd").modal('hide');
					fillMasterTable();
				}
			},
			error: function (response) {
				console.log(response.message);
			},
		})
	}

	function openEdit(id) {
		var data = {
			id : id
		}

		$.get('{{ url("fetch/workshop/jig/by_id") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#modalAdd").modal('show');

				$("#jig_id").val(id);
				$("#jig_code").val(result.jig.jig_code);
				$("#jig_name").val(result.jig.jig_name);
				$("#jig_part").val(result.jig.part_number);
				$("#jig_nama_part").val(result.jig.part_name);
				$("#jig_mat").val(result.jig.material);
				$("#jig_proses").val(result.jig.process).trigger('change');
				$("#jig_qty").val(result.jig.quantity);
			}
		})
	}

	function openDelete(id, jig_code) {
		if (confirm('Are you sure want to delete this jig "'+jig_code+'"?')) {
			var data = {
				id : id,
			}

			$.get('{{ url("delete/workshop/jig") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'Jig Has Been Deleted');

					fillMasterTable();
				}
			})
		}
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