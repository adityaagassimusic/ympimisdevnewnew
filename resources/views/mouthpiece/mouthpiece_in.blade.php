@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="employee_name">
		<input type="hidden" id="tag_kanban">

		<div class="col-xs-10 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
			<input type="text" id="operator_name" placeholder="Operator" style="width: 100%;font-size: 17px;text-align:center;padding: 10px" disabled="">
		</div>
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="qr_checksheet" placeholder="Scan Kanban Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-danger" onclick="clearAll()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
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
		<div class="col-xs-12" style="text-align: center;padding-top: 10px">
			<div class="row">
				<div class="col-md-1">
				</div>
				<div class="col-md-10" style="padding-right: 0px;padding-left: 0px">
					<table class="table table-bordered table-stripped" id="tableListCS" style="width: 100%;" >
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th colspan="8" style="font-size: 25px">LIST ITEM</th>
							</tr>
							<tr>
								<th style="width: 10%;">#</th>
								<th style="width: 10%;">GMC</th>
								<th style="width: 30%;">MATERIAL DESCRIPTION</th>
								<th style="width: 10%;">STORAGE LOCATION</th>
								<th style="width: 15%;">QTY</th>
								<th style="width: 5%;">UOM</th>
								<th style="width: 10%;">NO KANBAN</th>
								<th style="width: 10%;">#</th>
							</tr>					
						</thead>
						<tbody id="tableBodyListCS" style="background-color: white">
						</tbody>
					</table>
					<div id="btn_cs"></div>
				</div>
				<div class="col-md-1">
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px; padding-top: 20px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<center><span style="font-size: 25px;text-align: center;font-weight: bold;">TRANSACTION HISTORY</span> </center>
						<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">Material</th>
									<th style="width: 1%">Desc</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 1%">Loc</th>
									<th style="width: 3%">By</th>
									<th style="width: 3%">At</th>
								</tr>
							</thead>
							<tbody id="tableHistoryBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card / Ketik NIK" required="">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});
		FetchList();
		fillResult();
	});

	function Reload() {
		location.reload();
	}

	function clearAll(){
		$('#employee_id').val('');
		$('#tag_kanban').val('');
		$('#operator').val('');
		$('#qr_checksheet').val('');
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/kd_mouthpiece/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#employee_name').val(result.employee.name);
						$('#emp_id').text(result.employee.employee_id);
						$('#emp_name').text(result.employee.name);	
						$('#operator_name').val(result.employee.employee_id+' - '+result.employee.name);	
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_checksheet').val('');
						$('#qr_checksheet').focus();
						openSuccessGritter('Success!', 'ID operator sesuai.');
						audio_ok.play();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						audio_error.play();
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

	$('#qr_checksheet').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_checksheet").val().length == 10){
				// if(confirm("Item yang anda input akan menjadi stock dan item ini otomatis ter CS")){
				var tag_kanban = $('#qr_checksheet').val();
				var employee_id = $('#employee_id').val();
				var employee_name = $('#employee_name').val();
				var data = {
					tag_kanban:tag_kanban,
					employee_id:employee_id,
					employee_name:employee_name
				}
					// $.post('{{ url("save/stock/mouthpiece") }}', data, function(result, status, xhr){
				$.post('{{ url("save/list/mouthpiece") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success', 'Berhasil Menambah Stock.');
						audio_ok.play();
						$('#qr_checksheet').val("");
						$('#qr_checksheet').focus();
						FetchList();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#qr_checksheet').val("");
						$('#qr_checksheet').focus();
					}
				});
				// }
				// else{
				// 	return false;
				// }
			}
			else{
				openErrorGritter('Error!', 'Kanban tidak valid.');
				audio_error.play();
				$("#qr_checksheet").val("");
			}			
		}
	});

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

	function FetchList(){
		$.get('<?php echo e(url("list/cs/mouthpiece")); ?>', function(result, status, xhr){
			if(result.status){

				$('#tableListCS').DataTable().clear();
				$('#tableListCS').DataTable().destroy();
				var tableData = '';
				$('#tableBodyListCS').html("");
				$('#tableBodyListCS').empty();

				var count = 1;
				$.each(result.data, function(key, value) {

					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.desc +'</td>';
					tableData += '<td>'+ value.issue +'</td>';
					// tableData += '<td>'+ value.qty +'</td>';
					tableData += '<td><input type="text" class="form-control numpad" id="qty" style="width: 100%; font-size: 20px; text-align: center; color: red" value='+value.qty+' onChange="UpdateQty(\''+value.id+'\', this.value)"></td>';
					tableData += '<td>'+ value.uom +'</td>';
					tableData += '<td> No Kanban '+ value.no_kanban +'</td>';
					tableData += '<td><button type="button" class="btn btn-danger btn-xs" onClick="DeleteList(\''+value.id+'\')"><i class="fa fa-trash"></i> Delete</button></td>';
					tableData += '</tr>';

					count += 1;
				});

				$('#tableBodyListCS').append(tableData);
				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				if (result.data.length > 0) {
					$('#btn_cs').html('<button class="btn btn-success" onclick="CSMaterial()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">SIMPAN</button>');
				}else{
					$('#btn_cs').html('');
				}
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fillResult() {
		$.get('{{ url("fetch/mouthpiece/log") }}', function(result, status, xhr){
			if(result.status){
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableHistoryBody').html("");
				var tableData = "";
				if (result.resumes.length > 0) {
					$.each(result.resumes, function(key, value){
						$.each(result.emp, function(key2, value2){
							if (value.created_by == value2.employee_id) {

								tableData += '<tr>';
								tableData += '<td>'+ value.gmc +'</td>';
								tableData += '<td>'+ value.desc +'</td>';
								tableData += '<td>'+ value.qty +'</td>';
								tableData += '<td>VN91</td>';
								tableData += '<td>'+ value2.name +'</td>';
								tableData += '<td>'+ value.created_at +'</td>';
								tableData += '</tr>';

							}
						});
					});
				}
				$('#tableHistoryBody').append(tableData);

				$('#tableHistory tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				var table = $('#tableHistory').DataTable({
					'dom': 'Bfrtip',
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
					'searching'   	: true,
					'ordering'		: true,
					'order': [],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function CSMaterial(){
		var employee_name = $('#emp_name').text();
		var data = {
			employee_name:employee_name
		}
		if(confirm("Aoakah anda yakin akan menyimpan list ke dalam stock?")){
			$('#loading').show();
			$.post('{{ url("save/stock/mouthpiece") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success', 'Berhasil menambah stock, Material ter CS secara otomatis.');
					audio_ok.play();
					$('#qr_checksheet').val("");
					$('#qr_checksheet').focus();
					FetchList();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', 'Gagal Menyimpan, Pastikan Kanban Sesuai.	');
					$('#qr_checksheet').val("");
					$('#qr_checksheet').focus();
				}
			});
		}
	}

	function DeleteList(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('{{ url("delete/list/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success', 'Berhasil Menghapus List.');
				audio_ok.play();
				$('#qr_checksheet').val("");
				$('#qr_checksheet').focus();
				FetchList();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', 'Gagal Menyimpan, Pastikan Kanban Sesuai.	');
				$('#qr_checksheet').val("");
				$('#qr_checksheet').focus();
			}
		});
	}

	function UpdateQty(id, value){
		$('#loading').show();
		var data = {
			id:id,
			value:value
		}
		$.post('{{ url("update/qty/list/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success', 'Berhasil mengubah QTY per lot.');
				audio_ok.play();
				$('#qr_checksheet').val("");
				$('#qr_checksheet').focus();
				FetchList();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', 'Gagal Menyimpan, Pastikan Kanban Sesuai.	');
				$('#qr_checksheet').val("");
				$('#qr_checksheet').focus();
			}
		});
	}

</script>
@endsection