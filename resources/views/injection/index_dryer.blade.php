<?php $__env->startSection('stylesheets'); ?>
<link href="<?php echo e(url("css/jquery.gritter.css")); ?>" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
		/*cursor: pointer;*/
		background-color: #7dfa8c;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	
	#loading { display: none; }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
	<h1>
		<?php echo e($title); ?>
		<small><span class="text-purple"> <?php echo e($title_jp); ?></span></small>
		<!-- <button class="btn btn-primary pull-right" onclick="showModalAdjustment()" style="font-weight: bold;font-size: 20px"><i class="fa fa-exchange"></i> Dryer Adjustment</button> -->
	</h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-5">
			<span style="font-weight: bold; font-size: 16px;">Scan ID Card:</span>
			<div class="input-group" id="scan_tag" style="padding-bottom: 10px">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;font-size: 17px;height: 30px" class="form-control" id="tag" name="tag" placeholder="Scan ID Card / Ketik NIK" required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div class="input-group" id="scan_tag_success" style="padding-bottom: 10px">
				<div class="col-xs-4">
					<div class="row">
						<input type="text" id="op" style="width: 100%; height: 30px; font-size: 17px; text-align: center;" disabled placeholder="Employee ID">
					</div>
				</div>
				<div class="col-xs-5">
					<div class="row" style="padding-left: 5px">
						<input type="text" id="op2" style="width: 100%; height: 30px; font-size: 17px; text-align: center;" disabled placeholder="Name">
					</div>
				</div>
				<div class="col-xs-3">
					<div class="row" style="padding-left: 5px">
						<button class="btn btn-danger" onclick="cancelEmp()" style="width: 100%;height: 30px;font-size: 14px;vertical-align: middle;">
							<b>CLEAR</b>
						</button>
					</div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">ITEM RESIN:</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Material</th>
								<th style="width: 7%;">Description</th>
								<th style="width: 1%;">Color</th>
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
				<div class="col-xs-12" style="text-align: center;">
					<span style="font-weight: bold;font-size: 30px;width: 100%;">DETAIL RESIN</span>
				</div>
				<div class="col-xs-6" style="padding-top: 10px">
					<span style="font-weight: bold; font-size: 16px;">Dryer:</span>
					<select name="dryer" id="dryer" class="form-group" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" data-placeholder="Select Dryer">
						<option value="-">Pilih Dryer</option>
						<option value="1">1</option>
						<option value="2 & 6">2 & 6</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="7">7</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
			            <option value="12">12</option>
			            <option value="13">13</option>
			            <option value="14">14</option>
			            <option value="16">16</option>
			            <option value="17 (Tangki)">17 (Tangki)</option>
			            <option value="18 (Tangki)">18 (Tangki)</option>
			            <option value="19 (Tangki)">19 (Tangki)</option>
					</select>
				</div>
				<div class="col-xs-6" style="padding-top: 10px">
					<span style="font-weight: bold; font-size: 16px;">Material:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12" style="padding-top: 10px">
					<span style="font-weight: bold; font-size: 16px;">Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-6" style="padding-top: 20px">
					<span style="font-weight: bold; font-size: 16px;">Color:</span>
					<input type="text" id="color" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-6" style="padding-top: 20px">
					<span style="font-weight: bold; font-size: 16px;">Lot Number:</span>
					<input type="text" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" placeholder="Lot Number" id="lot_number" onkeyup="upperLot(this.value)">
				</div>
				<div class="col-xs-12" style="padding-top: 20px">
					<span style="font-weight: bold; font-size: 16px;">Add Quantity:</span>
				</div>
				<div class="col-xs-6">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
						</div>
						<input id="quantity" style="font-size: 40px; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

						<div class="input-group-btn">
							<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<button class="btn btn-primary" onclick="inputResin()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
						<i class="fa fa-send"></i> INPUT
					</button>
				</div>
				
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;" id="">HISTORY PEMAKAIAN RESIN (<?php echo date('d-M-Y', strtotime('-1 week')); ?> - <?php echo date('d-M-Y'); ?>)</span>
					<table class="table table-hover table-striped table-bordered" id="tableResume">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Material</th>
								<th style="width: 1%;">Dryer</th>
								<th style="width: 2%;">Lot</th>
								<th style="width: 1%;">Qty</th>
								<th style="width: 2%;">By</th>
								<th style="width: 1%;">At</th>
								<th style="width: 2%;background-color: #ffe3e3;">Material (Before)</th>
								<th style="width: 2%;background-color: #ffe3e3;">Lot (Before)</th>
								<th style="width: 1%;background-color: #ffe3e3;">Qty (Before)</th>
								<th style="width: 2%;background-color: #ffe3e3;">By (Before)</th>
								<th style="width: 2%;background-color: #ffe3e3;">Status</th>
							</tr>
						</thead>
						<tbody id="tableBodyResume">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

<div class="modal fade" id="modalDryerAdjustment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffac26;color: white">
					<span style="font-weight: bold; font-size: 3vw;">Dryer Adjustment</span><br>
				</center>
				<hr>
				<div class="modal-body" style="min-height: 300px; padding-bottom: 5px;">
					<div class="col-xs-5">
						<div class="row">
							@foreach($dryer as $dryer)
								<div class="col-xs-6" style="padding-bottom: 10px">
									<button class="btn btn-info" onclick="getDryer(this.id)" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;" id="{{$dryer->dryer}}">
										Dryer {{$dryer->dryer}}
									</button>
								</div>
							@endforeach
						</div>
					</div>
					<div class="col-xs-7">
						<div class="col-xs-12">
							<input type="hidden" id="dryer_id" style="width: 100%; height: 50px; font-size: 30px; text-align: center;">
							<div class="row">
								<div class="col-xs-6">
									<span style="font-weight: bold; font-size: 16px;">Dryer:</span>
									<input type="text" id="dryer_adjust" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
								</div>
								<div class="col-xs-6">
									<span style="font-weight: bold; font-size: 16px;">Color:</span>
									<input type="text" id="color_adjust" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-6">
									<span style="font-weight: bold; font-size: 16px;">Lot Number:</span>
									<input type="text" id="lot_number_adjust" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
								</div>
								<div class="col-xs-6">
									<span style="font-weight: bold; font-size: 16px;">Qty:</span>
									<input type="text" id="qty_adjust" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<span style="font-weight: bold; font-size: 16px;">Machine:</span>
									<select name="machine_adjust" id="machine_adjust" class="form-group" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" data-placeholder="Select Machine">
										@foreach($mesin as $mesin)
										<option value="{{$mesin}}">{{$mesin}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="dryerFooter">
							<button class="btn btn-success pull-right" onclick="saveAdjustment()">
								<i class="fa fa-arrow-right"></i> SAVE
							</button>
							<button class="btn btn-danger pull-left" data-dismiss="modal">
								CLOSE
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
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

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		fetchResinList();
		fetchResumeResin();
		$('#tag').focus();
		$('#tag').val("");
		$('#op').val("");
		$('#op2').val("");
		$('#scan_tag_success').hide();
		$('#material_number').val("");
		$('#material_description').val("");
		$('#part').val("");
		$('#color').val("");
		$('#quantity').val("0");
		$('#dryer').prop('disabled',true);
		$('#lot_number').prop('disabled',true);
		$('#lot_number').val('');
		$('#machine_adjust').prop('disabled',true);
		$('#dryer_id').val('');
		$('#dryer_adjust').val('');
		$('#part_adjust').val('');
		$('#color_adjust').val('');
		$('#qty_adjust').val('');
		$('#lot_number_adjust').val('');
		$('#machine_adjust').val(null).trigger('change');
		$('#dryer').val("-").trigger('change');
	});

	function upperLot(value) {
		$('#lot_number').val(value.toUpperCase());
		$('#lot_number').focus();
	}

	function showModalAdjustment() {
		if ($('#op').val() == '') {
			openErrorGritter('Error!','Scan ID Card First!');
			$('#tag').focus();
			$('#tag').val('');
		}else{
			$('#modalDryerAdjustment').modal('show');
			$('#dryer_id').val('');
			$('#dryer_adjust').val('');
			$('#part_adjust').val('');
			$('#color_adjust').val('');
			$('#qty_adjust').val('');
			$('#machine_adjust').prop('disabled',true);
			$('#lot_number_adjust').val('');
			$('#machine_adjust').val(null).trigger('change');
		}
	}

	function getDryer(dryer) {
		if (dryer == '-') {
			$('#material_number').val('');
			$('#material_description').val('');
			$('#color').val('');
			$('#lot_number').val('');
			$('#quantity').val('');
		}
		else{
			$('#loading').show();
			var data = {
				dryer:dryer
			}
			$.get('{{ url("index/injection/fetch_dryer") }}', data, function(result, status, xhr){
				if(result.status){
					// $('#dryer_id').val(result.dryer.id);
					$('#material_number').val(result.dryer.material_number);
					$('#material_description').val(result.dryer.material_description);
					$('#color').val(result.dryer.color);
					$('#lot_number').val(result.dryer.lot_number);
					$('#quantity').val(result.dryer.qty);
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function saveAdjustment() {
		var data = {
			id:$('#dryer_id').val(),
			machine:$('#machine_adjust').val(),
		}

		$.post('{{ url("index/injection/update_dryer") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success',result.message);
				$('#dryer_id').val('');
				$('#dryer_adjust').val('');
				$('#part_adjust').val('');
				$('#color_adjust').val('');
				$('#qty_adjust').val('');
				$('#machine_adjust').prop('disabled',true);
				$('#lot_number_adjust').val('');
				$('#machine_adjust').val(null).trigger('change');
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function cancelEmp() {
		$('#op').val("");
		$('#op2').val("");
		$('#scan_tag').show();
		$('#scan_tag_success').hide();
		$('#tag').focus();
		$('#tag').val("");
		$('#material_number').val('');
		$('#material_description').val('');
		$('#color').val('');
		$('#lot_number').val('');
		$('#lot_number').prop('disabled',true);
		$('#dryer').prop('disabled',true);
		$('#dryer').val("-").trigger('change');
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 8){
				var data = {
					employee_id : $("#tag").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#scan_tag').hide();
						$('#scan_tag_success').show();
						$('#op').val(result.employee.employee_id);
						$('#op2').val(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#lot_number').removeAttr('disabled');
						$('#dryer').removeAttr('disabled');
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#tag").val("");
			}			
		}
	});

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}

	function fetchResin(material_number,material_description,color) {
		if ($('#op').val() == '') {
			openErrorGritter('Error!', "Scan ID Card First!");
			$('#tag').focus();
			return false;
		}else if ($('#dryer').val() == '-') {
			openErrorGritter('Error!', "Pilih Dryer Dulu.");
			return false;
		}else{
			$('#dryer').removeAttr('disabled');
			$('#material_number').val(material_number);
			$('#material_description').val(material_description);
			$('#color').val(color);
		}
	}

	function fetchResinList(){
		var data = {
			color:''
		}
		$.get('{{ url("index/injection/fetch_resin") }}', data,function(result, status, xhr){
			if(result.status){
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					tableData += '<tr onclick="fetchResin(\''+value.gmc+'\''+','+'\''+value.part_name+'\''+','+'\''+value.color+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.part_name +'</td>';
					tableData += '<td>'+ value.color +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyList').append(tableData);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchResumeResin(){
		$("#loading").show();
		$.get('{{ url("index/injection/fetch_resume_resin") }}', function(result, status, xhr){
			if(result.status){
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$('#tableBodyResume').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ count +'</td>';
					tableData += '<td>'+ value.material_number +' - '+ value.material_description +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.dryer +'</td>';
					tableData += '<td>'+ value.lot_number +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.qty +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.created +'</td>';
					tableData += '<td style="background-color:#ffe3e3">'+ (value.matnumbefore || '') +' - '+ (value.matdesbefore || '') +'</td>';
					tableData += '<td style="background-color:#ffe3e3">'+ (value.lotbefore || '') +'</td>';
					tableData += '<td style="background-color:#ffe3e3;text-align:right;padding-right:7px;">'+ (value.qtybefore || '') +'</td>';
					tableData += '<td style="background-color:#ffe3e3">'+ (value.namebefore || '') +'</td>';
					if (value.matnumbefore == null) {
						tableData += '<td style="background-color:#ffe3e3"></td>';
					}else{
						tableData += '<td style="background-color:#ffe3e3">'+ value.status +'</td>';
					}
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyResume').append(tableData);

				var table = $('#tableResume').DataTable({
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
					'pageLength': 5,
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
				$("#loading").hide();
			}
			else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function inputResin() {
		$('#loading').show();
		if ($('#material_number').val() == "" || $('#quantity').val() == "0" || $('#dryer').val() == "-" || $('#lot_number').val() == "") {
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua Data Harus Diisi.');
			return false;
		}else{
			var data = {
				material_number:$('#material_number').val(),
				material_description:$('#material_description').val(),
				part:$('#part').val(),
				color:$('#color').val(),
				qty:$('#quantity').val(),
				lot_number:$('#lot_number').val(),
				employee_id:$('#op').val(),
				dryer:$('#dryer').val()
			}

			$.post('{{ url("input/injection/resin") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#material_number').val("");
					$('#material_description').val("");
					$('#part').val("");
					$('#color').val("");
					$('#quantity').val("0");
					$('#lot_number').val('');
					$('#lot_number').prop("disabled",true);
					$('#dryer').prop("disabled",true);
					fetchResinList();
					fetchResumeResin();
					$('#dryer').val("-").trigger('change');
					$('#loading').hide();
					location.reload();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>