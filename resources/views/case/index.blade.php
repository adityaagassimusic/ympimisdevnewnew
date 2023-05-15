@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	#loading {
		display: none;
	}
	#qr_item:hover{
		color:#ffffff
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<!-- <a data-toggle="modal" data-target="#modalQr" class="btn btn-primary btn-lg" style="color:white;">
				&nbsp;<i class="glyphicon glyphicon-qrcode"></i>&nbsp;&nbsp;&nbsp;Scan Scanner&nbsp;
			</a>

			<a data-toggle="modal" data-target="#modalScan" class="btn btn-success btn-lg" style="color:white;">
				&nbsp;<i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan Camera&nbsp;
			</a> -->			
		</li>
	</ol>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<!-- <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div> -->
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-5">
			<div class="box">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM CASE :</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Material</th>
								<th style="width: 7%;">Deskripsi</th>
								<th style="width: 1%;">Stok</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
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
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Operator</span>
					<input type="text" id="employee_detail" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly>
					<input type="hidden" id="employee_id">
					<input type="hidden" id="employee_name">
				</div>

				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Deskripsi:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" readonly>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Jumlah Qty Pengambilan:</span>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
								</div>
								<input id="quantity" style="font-size: 3vw; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
								</div>
							</div>
						</div>
						<div class="col-xs-5" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="submitPengambilan()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								<i class="fa fa-save"></i> SUBMIT
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">History Pengambilan Case (<?php echo e(date('d-M-Y')); ?>)</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead>
									<tr>
										<th style="width: 1%;">#</th>
										<th style="width: 1%;">Material</th>
										<th style="width: 6%;">Description</th>
										<th style="width: 1%;">Date</th>
										<th style="width: 1%;">Qty</th>
										<th style="width: 1%;">Creator</th>
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>



<div class="modal fade" id="modalQr">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan Slip Return</h3></center>
			</div>
			<div class="modal-body" style="padding-bottom: 75px;">
				<div class="row">
					<div class="col-xs-12">
						<center>
							<div id="div_qr_item">
								<input id="qr_item" type="text" style="border:0; width: 100%; text-align: center; color: #3c3c3c; font-size: 2vw;">
							</div>
						</center>							
					</div>
					<div class="receiveReturn" style="width:100%; padding-left: 2%; padding-right: 2%;">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalInputor">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Inputor</label>
							<select class="form-control select2" name="inputor" onchange="inputorInput()" id='inputor' data-placeholder="Select Inputor" style="width: 100%;">
								<option value="">Select Inputor</option>
								@foreach($employees as $employee)
								<option value="{{ $employee->employee_id }}_{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


@endsection
@section('scripts')
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	fetchCaseList();

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 37.5%; z-index: 10000000; border: 2px solid grey;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){
		$(this).find('.del').addClass('btn-default');
		$(this).find('.clear').addClass('btn-default');
		$(this).find('.cancel').addClass('btn-default');		
		$(this).find('.done').addClass('btn-success');
		$(this).find('.neg').addClass('btn-default');
		$('.neg').css('display', 'block');
	};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear : true,
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('#modalInputor').modal({
				backdrop: 'static',
				keyboard: false
			});
	});

	$('#modalInputor').on('hidden.bs.modal', function () {
		$('#quantity').focus();
	});

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}

	function inputorInput(){
		$('#modalInputor').modal('hide');

		var inputor = $('#inputor').val().split("_");

		$('#employee_detail').text('');
		$('#employee_detail').val(inputor[0]+' - '+inputor[1]);
		$('#employee_id').text('');
		$('#employee_id').val(inputor[0]);
		$('#employee_name').text('');
		$('#employee_name').val(inputor[1]);

		$("#serial").focus();
	};


	function submitPengambilan(){
		$('#loading').show();
		var material = $('#material_number').val();
		var description = $('#material_description').val();
		var quantity = $('#quantity').val();

		if(material == ''){
			$('#loading').hide();
			openErrorGritter('Error!', 'Pilih case yang akan diambil');
			return false;
		}
		if(quantity == '' || quantity < 1){
			$('#loading').hide();
			openErrorGritter('Error!', 'Isikan quantity case yang akan diambil');
			return false;
		}

		var data = {
			material:material,
			description:description,
			quantity:quantity
		}
		$.post('<?php echo e(url("confirm/case")); ?>', data, function(result, status, xhr){
			if(result.status){
				fetchResume();
				fetchCaseList();
				$('#material_number').val("");
				$('#material_description').val("");
				$('#quantity').val(0);

				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchCase(id){
		var ids = id.split("_");

		$('#material_number').val(ids[0]);
		$('#material_description').val(ids[1]);
	}


	function fetchResume(){
		
		$.get('<?php echo e(url("fetch/case/resume")); ?>', function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.created_at +'</td>';
				tableData += '<td>'+ value.qty +'</td>';
				tableData += '<td>'+ value.created_by +'</td>';
				tableData += '</tr>';

				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}


	function fetchCaseList(){
		fetchResume();
		$.get('<?php echo e(url("fetch/case/list")); ?>', function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;

				$.each(result.lists, function(key, value) {
					tableData += '<tr id="'+value.material_number+'_'+value.material_description+'" onclick="fetchCase(id)">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.qty +'</td>';
					tableData += '</tr>';
					count += 1;
				});
				$('#tableBodyList').append(tableData);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var tableList = $('#tableList').DataTable({
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				tableList.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tableList tfoot tr').appendTo('#tableList thead');

				openSuccessGritter('Success!', result.message);
				// $('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});

	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

</script>
@stop