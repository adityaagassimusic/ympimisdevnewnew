@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $loc }}">
	<input type="hidden" id="employee_id">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-xs-4 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="op" style="width: 100%;text-align:center;padding: 10px" readonly>
				</div>
				<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="op2" style="width: 100%;text-align:center;padding: 10px" readonly>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="tag_product" placeholder="Scan Kanban Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						CANCEL
					</button>
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-warning" onclick="location.reload()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						REFRESH
					</button>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;padding-top: 10px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<center><span style="font-size: 25px;text-align: center;font-weight: bold;">CUCI ENTHOLE HISTORY</span> </center>
						<table id="tableEnthol" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">#</th>
									<th style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Material</th>
									<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Loc</th>
									<th style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">Qty</th>
									<th style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">At</th>
								</tr>
							</thead>
							<tbody id="bodyEntholLog">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						</div>
					</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var counter = 0;
	var arrPart = [];
	var intervalCheck;

	jQuery(document).ready(function() {
		// fetchKanbanHistory();
      	$('body').toggleClass("sidebar-collapse");
		$("#tag_product").val("");
		// $('#tag_product').focus();

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#op").val('');
		$("#op2").val('');
		$("#operator").val('');
		$("#operator").focus();

		fetchEntholLog();
	});

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
				$('#tag_product').prop('disabled',true);
				var tag = $('#tag_product').val();
				var data = {
					tag : $("#tag_product").val(),
					loc : $("#location").val(),
					employee_id : $('#employee_id').val(),
					type : 'enthole',
				}
				$.get('{{ url("scan/enthol/kanban") }}', data, function(result, status, xhr){
					if (result.status) {
						openSuccessGritter('Success',result.message);
						$('#tag_product').removeAttr('disabled');
						$('#tag_product').val('');
						$('#tag_product').focus();
						$('#loading').hide();
						fetchEntholLog();
					}else{
						$('#tag_product').removeAttr('disabled');
						$('#tag_product').val('');
						$('#tag_product').focus();
						openErrorGritter('Error!',result.message);
						$('#loading').hide();
					}
				});
		}
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val(),
				}
				
				$.get('{{ url("scan/body/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').val(result.employee.employee_id);
						$('#op2').val(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$('#tag_product').focus();
						$('#tag_product').val('');
						// fetchBodyKensa(result.employee.employee_id);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}			
		}
	});

	function cancel() {
		$('#tag_product').removeAttr('disabled');
		$('#tag_product').val('');
		$('#tag_product').focus();
	}

	function fetchEntholLog() {
		$('#loading').show();
		var data = {
			location : $("#location").val(),
		}
		$.get('{{ url("fetch/enthol/log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableEnthol').DataTable().clear();
				$('#tableEnthol').DataTable().destroy();
				$('#bodyEntholLog').html('');
				var bodyEnthol = '';

				for(var i = 0; i < result.enthol.length;i++){
					bodyEnthol += '<tr>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+(i+1)+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].material_number+' - '+result.descs[i]+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].location+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].quantity+'</td>';
					bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">'+result.enthol[i].created_at+'</td>';
					bodyEnthol += '</tr>';
				}
				$('#bodyEntholLog').append(bodyEnthol);
				$('#loading').hide();

				var table = $('#tableEnthol').DataTable({
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
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection