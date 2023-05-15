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
	cursor: pointer;
	background-color: #7dfa8c;
}

table.table-bordered{
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	font-size: 13px;
	text-align: center;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	vertical-align: middle;
	padding:10px;
	font-size: 13px;
	text-align: center;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid black;
	padding:0;
}

table.table-bordered1{
	border:1px solid black;
}
table.table-bordered1 > thead > tr > th{
	border:1px solid black;
	font-size: 12px;
	text-align: center;
}
table.table-bordered1 > tbody > tr > td{
	border:1px solid black;
	vertical-align: middle;
	padding:0;
	font-size: 12px;
	text-align: center;
	padding:10px;
}
table.table-bordered1 > tfoot > tr > th{
	border:1px solid black;
	padding:0;
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
	background-color: #ffd8b7;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
	background-color: #FFD700;
}


input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
	-webkit-appearance: none;
	margin: 0;
}

input[type=number] {
	-moz-appearance:textfield;
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
			<div class="box" style="margin-top: 15px;">
				<div class="box-body">
					<table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 5%;">GMC</th>
								<th style="width: 20%;">DESKRIPSI</th>
								<th style="width: 5%;">UOM</th>
								<th style="width: 5%;">LOC</th>
								<th style="width: 8%;">CAT</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-4">
					<label style="font-weight: bold; font-size: 16px;">GMC : </label>
					<input type="text" id="material" style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" disabled value="">
				</div>
				<div class="col-xs-4">
					<label style="font-weight: bold; font-size: 16px;">UOM : </label>
					<input type="text" id="uom" style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" disabled value="">
				</div>
				<div class="col-xs-4">
					<label style="font-weight: bold; font-size: 16px;">From Location : </label>
					<input type="text" id="issue" name="issue_location" style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">Part's Name : </label>
					<input type="text" id="description" name="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center; color: red" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">Category : </label>
					<input type="text" id="remark" name="remark" style="width: 100%; height: 50px; font-size: 24px; text-align: center; color: red" disabled>
				</div>

				<div class="col-xs-3">
					<label style="font-weight: bold; font-size: 16px;">Type Material : <span class="text-red">*</span></label>
					<select class="form-control select2" id="type_material" name="type_material" data-placeholder='Select' onchange="SelectTypeMaterial(this.value)" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						<option value="Normal">Normal</option>
						<option value="Trial">Trial</option>
					</select>
				</div>

				<div class="col-xs-3">
					<label style="font-weight: bold; font-size: 16px;">To Location : <span class="text-red">*</span></label>
					<!-- <span style="font-weight: bold; font-size: 16px;">To Location:</span> -->
					<select class="form-control select2" id="receive_location" name="receive_location" data-placeholder='Select' onchange="SelectReason(this.value)" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						@foreach($reicive as $reicive)
						<option value="{{ $reicive }}">{{ $reicive }}</option>
						@endforeach
					</select>
					<!-- <select class="form-control select2" id="receive_location" name="receive_location" data-placeholder='Select' style="width: 100%" onchange="SelectReason(this.value)" required>
					</select> -->
				</div>
				<div class="col-xs-3">
					<label style="font-weight: bold; font-size: 16px;">Reason : <span class="text-red">*</span></label>
					<select class="form-control select2" id="reason" name="reason" data-placeholder='Select' style="width: 100%" required>
					</select>
					<!-- <select class="form-control select2" id="reason" name="reason" data-placeholder='Select' style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						@foreach($reason as $rea)
						<option value="{{$rea->reason}}">{{$rea->reason}} - {{$rea->reason_name}}</option>
						@endforeach
					</select> -->
				</div>

				@if($emp_dept->employee_id == 'PI0004012' || $emp_dept->employee_id == 'PI2101044' || $emp_dept->employee_id == 'PI9910004' || $emp_dept->employee_id == 'PI9808013' || $emp_dept->employee_id == 'PI0904005')
				<div class="col-xs-3">
					<label style="font-weight: bold; font-size: 16px;">No Invoice : </label>
					<input type="text" id="invoice" name="invoice" style="width: 100%; height: 35px; font-size: 20px; text-align: center">
				</div>
				@endif

				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Summary: <span class="text-red">*</span></span>
				</div>

				<div class='col-xs-12' id="div_tambah_defect_1">
					<input type="text" name="lop" id="lop" value="1" hidden>
					<input type="text" name="kondisi" id="kondisi" hidden>
					<div class="col-xs-5" style="margin-bottom : 5px; padding-left: 0">
						<select class="form-control select2 select_defect" id="select_defect_1" name="select_defect_1" data-placeholder="Select Defect" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
							<option value="">&nbsp;</option>
							@foreach($defect as $def)
							<option value="{{$def->defect}}">{{$def->defect}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-xs-3" style="padding-left: 0">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-danger" style="font-size: 20px; height: 35px; text-align: center;"  onclick="minusCount('1')"><span class="fa fa-minus"></span></button>
							</div>
							<input id="qty_defect_1" name="qty_defect_1" style="font-size: 20px; height: 35px; text-align: center;" type="number" class="form-control numpad trial" value="0" onchange="plusCount('0')">

							<div class="input-group-btn">
								<button type="button" class="btn btn-success" style="font-size: 20px; height: 35px; text-align: center;" onclick="plusCount('1')"><span class="fa fa-plus"></span></button>
							</div>
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 10px">
						<button style='width: 100%' class="btn btn-success pull-right" type="button" onclick="TambahDefect('div_tambah_defect','lop')">Add Defect</button>
					</div>
				</div>

				<div id="div_tambah_defect"></div>

				
				<!-- <div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Summary: <span class="text-red">*</span></span>
					<textarea id="summary" name="summary" style="width: 100%; height: 100px; font-size: 12px; text-align: left" required></textarea>
				</div> -->

				<div class="col-xs-6" hidden="hidden">
					<div class="row">
						<div class="col-xs-6">
							<label style="font-weight: bold; font-size: 16px;">SPT : </label>
							<input type="text" id="spt" name="spt" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled value="">
						</div>
					</div>
				</div>
				<div class="col-xs-6" hidden="hidden">
					<div class="row">
						<div class="col-xs-6">
							<label style="font-weight: bold; font-size: 16px;">VALCL : </label>
							<input type="text" id="valcl" name="valcl" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled value="">
						</div>
					</div>
				</div>

				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">QTY : <span class="text-red">*</span></label>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-8">
							<div class="input-group">
								<!-- <div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"  onclick="minusCount()"><span class="fa fa-minus"></span></button>
								</div> -->
								<input id="quantity" name="quantity" style="font-size: 40px; height: 60px; text-align: center;" type="number" class="form-control" readonly value="0">

								<!-- <div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;" onclick="plusCount()"><span class="fa fa-plus"></span></button>
								</div> -->
							</div>
						</div>
						<div class="col-xs-4" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="printScrap()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">CETAK
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">List Scrap</span>
							<!-- <button style="margin: 1%;" class="btn btn-info pull-right" onClick="refreshTable();"><i class="fa fa-refresh"></i> Refresh Tabel</button> -->
							<table class="table table-hover table-striped table-bordered1" id="tableResume">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Slip Number</th>
										<th>Part's Name</th>
										<th>Receive Location</th>
										<th>Category</th>
										<th>Qty</th>
										<th>Creator</th>
										<th>Created</th>
										<th>Cancel</th>
										<th>Reprint</th>
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

<div class="modal fade" id="modalLocation">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a;">Pilih Lokasi Anda</h3></center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<select id="selectLocation" class="form-control select2" onchange="fetchScrapList(value)" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($storage_locations as $storage_location)
							<option value="{{ $storage_location }}">{{ $storage_location }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
	var no = 2;

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
		$('body').toggleClass("sidebar-collapse");
		clearAll();

		$('.select2').select2();
		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		// setInterval(function(){
			// fetchResume($('#location').val());
		// }, 15000);

	});

	function SelectTypeMaterial(value){
		var data = {
			type_material:$('#type_material').val()
		}
		$.get('{{ url("select/scrap/type") }}',data, function(result, status, xhr){
			if(result.status){
				$('#receive_location').show();
				$('#receive_location').html('');
				$('#reason').html('');
				loc = '';

				if (value == 'Normal') {
					$.each(result.loc, function(key, value) {
						loc += '<option value="'+value+'">'+value+'</option>';
					});

					$('#receive_location').append(loc);
					$('#receive_location').val('').trigger('change');
				}else if (value == 'Trial'){
					loc += '<option value="MSCR">MSCR</option>';
					$('#receive_location').append(loc);

					re = '';
					re += '<option value="TRI">TRI - Trial</option>';
					$('#reason').append(re);					
				}
			}
		});
	}

	function SelectReason(value) {
		var data = {
			receive_location:$('#receive_location').val()
		}
		$.get('{{ url("select/scrap/reason") }}',data, function(result, status, xhr){
			if(result.status){
				$('#reason').show();
				$('#reason').html('');
				rea = '';

				rea += '<option value=""></option>';
				rea += '<option value="Material Kembali Ke WIP">Material Kembali Ke WIP</option>';

				$.each(result.reason, function(key, value) {
					rea += '<option value="'+value.reason+'">'+value.reason+' - '+value.reason_name+'</option>';
				});

				$('#reason').append(rea);
				$('#reason').val('').trigger('change');
			}
		});
    }


	function plusCount(no){
		// $('#quantity').val(parseInt($('#quantity').val())+1);
		$('#qty_defect_'+no+'').val(parseFloat($('#qty_defect_'+no+'').val())+1);

		var sum = 0;
		var leng = $('.trial').length;

		for (var i = 1; i <= leng; i++) {
			sum += parseFloat($('#qty_defect_'+i).val());
		}
		
		$('#quantity').val(sum);
	}

	function minusCount(no){
		// $('#quantity').val(parseInt($('#quantity').val())-1);
		$('#qty_defect_'+no+'').val(parseFloat($('#qty_defect_'+no+'').val())-1);

		var sum = 0;
		var leng = $('.trial').length;

		for (var i = 1; i <= leng; i++) {
			sum += parseFloat($('#qty_defect_'+i).val());
		}
		// console.log(sum);
		$('#quantity').val(sum);
	}

	function receiveReturn(video, data){
		$('#scanner').hide();
		$('#modalReceive').modal('hide');
		$(".modal-backdrop").remove();

		var x = {
			id:data
		}

		$.get('<?php echo e(url("fetch/scrap")); ?>', x, function(result, status, xhr){
			if(result.status){
				var re = "";
				$('#receiveReturn').html("");
				re += '<table style="text-align: center; width:100%;"><tbody>';
				re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.return.material_number+'</td></tr>';
				re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.return.receive_location+' -> '+result.return.issue_location+'</td></tr>';
				re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.return.material_description+'</td></tr>';
				re += '<tr><td style="font-size: 50px; font-weight: bold; background-color:black; color:white;" colspan="2">'+result.return.quantity+' PC(s)</td></tr>';
				re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.return.name+'</td></tr>';
				re += '<tr>';
				re += '<td><button id="reject+'+result.return.id+'" class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TOLAK</button></td>';
				re += '<td><button id="receive+'+result.return.id+'" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TERIMA</button></td>';
				re += '</tr>';
				re += '</tbody></table>';

				$('#receiveReturn').append(re);
			}
			else{
				$('#receiveReturn').html("");
				showCheck();
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function confirmReceive(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('<?php echo e(url("confirm/scrap")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#receiveScrap').html("");
				showCheck();
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function printScrap(){

		if(confirm("Apakah anda yakin akan mencetak slip scrap ini?")){

			var sum = [];
			var kondisi = $('#kondisi').val();
			var uom = $('#uom').val();
			var material = $('#material').val();
			var issue = $('#issue').val();
			var receive = $('#receive').val();
			var description = $('#description').val();
			var category = $('#remark').val();
			var quantity = $('#quantity').val();
			var reason = $('#reason').val();
			var receive_location = $('#receive_location').val();
			var summary = $('#summary').val();
			var category_reason = $('#category_reason').val();
			var spt = $('#spt').val();
			var valcl = $('#valcl').val();
			var invoice = $('#invoice').val();


			if (kondisi == '') {
				var select_deffect = $('#select_defect_1').val();
				var qty_deffect = $('#qty_defect_1').val();
				sum.push(select_deffect+'_'+qty_deffect);
			}else{
				$.each($('.select_defect'),function(i, obj) {
					var select_deffect = $(obj).val();
					var qty_deffect = $('.trial').eq(i).val();
					sum.push(select_deffect+'_'+qty_deffect);
				});

				// for (var i = 1; i < kondisi; i++) {
				// 	var select_deffect = $('#select_defect_'+i).val();
				// 	var qty_deffect = $('#qty_defect_'+i).val();
				// 	console.log(select_deffect);
				// 	sum.push(select_deffect+'_'+qty_deffect);
				// }
			}

			// $('#loading').show();

			if(material == '' || receive_location == '' || summary == '' || select_deffect == ''){
				$('#loading').hide();
				openErrorGritter('Error!', 'Isi data secara lengkap');
				return false;
			}
			if(quantity == '' || quantity == 0){
				$('#loading').hide();
				openErrorGritter('Error!', 'Isikan quantity yang akan di scrap');
				return false;
			}

			var data = {
				material:material,
				issue:issue,
				receive:receive,
				quantity:quantity,
				description:description,
				category:category,
				reason:reason,
				uom:uom,
				receive_location:receive_location,
				summary:summary,
				category_reason:category_reason,
				spt:spt,
				valcl:valcl,
				invoice:invoice,
				sum:sum
			}
			$.post('{{ url("print/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					fetchResume(issue);
					reset();
					no = 2;
					$('#loading').hide();
					openSuccessGritter('Success', result.message);
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			return false;
		}
	}

	function clearAll(){
		$("#selectLocation").prop('selectedIndex', 0).change();
		$("#category").prop('selectedIndex', 0).change();
		$('#material').val("");
		$('#uom').val("");
		$('#issue').val("");
		$('#description').val("");
		$("#receive_location").prop('selectedIndex', 0).change();
		$("#reason").prop('selectedIndex', 0).change();
		$('#quantity').val(0);
		$('#remark').val("");
		// $('#type_material').prop('selectedIndex', 0).change();
	}

	function fetchReturn(material, description, issue, spt, valcl, uom, remark){
		$('#material').val("");
		$('#uom').val("");
		$('#issue_location').val("");
		$('#material_description').val("");
		// $("#receive_location").prop('selectedIndex', 0).change();
		$("#reason").prop('selectedIndex', 0).change();
		$('#quantity').val(0);

		$('#material').val(material);
		$('#uom').val(uom);
		$('#description').val(description);
		$('#issue').val(issue);
		$('#spt').val(spt);
		$('#valcl').val(valcl);
		$('#remark').val(remark);
		// $('#type_material').val("").trigger('change');
		$('#receive_location').val("").trigger('change');
	}

	function reset(){
		$('#material').val("");
		$('#issue').val("");
		$('#receive').val("");
		$('#description').val("");
		// $('#category').val("").trigger('change');
		$('#reason').val("").trigger('change');
		$('#summary').val("");
		$('#category_reason').val("").trigger('change');
		$('#quantity').val(0);
		$('#spt').val("");
		$('#valcl').val("");
		$('#uom').val("");
		$('#remark').val("");
		$('#invoice').val("");
		// $('#type_material').val("").trigger('change');
		$('#receive_location').val("").trigger('change');
		$('#div_tambah_defect').empty();
		// $('#div_tambah_defect_1').empty();
		$('#select_defect_1').val("").trigger('change');
		$('#qty_defect_1').val(0);
		$('#quantity').val(0);
		$('#div_tambah_defect').empty();
		$('#div_tambah_defect').empty();
		$('#type_material').val("").trigger('change');
	}

	function reprintScrap(id){
		if(confirm("Apakah anda yakin akan mencetak ulang slip scrap ini?")){
			var data = {
				id:id,
				cat:'pending'
			}
			$.get('<?php echo e(url("reprint/scrap")); ?>', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			return false;
		}
	}

	function fetchResume(loc){
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/scrap/resume")); ?>', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
				if (value.remark == "0") {
					tableData += '<tr style="background-color: #F0E68C;">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.order_no +'</td>';
					tableData += '<td>'+value.material_number+'<br>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.category +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					if ((result.admin == 'S-MIS') || (result.admin == 'S-PRD') || (result.admin == 'S-PCH') || (result.qa == 'PI0904005')) {
						tableData += '<td><center><button class="btn btn-danger btn-sm" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					}else{
						tableData += '<td>-</td>';
					}
					tableData += '<td><center><button class="btn btn-primary btn-sm" onclick="reprintScrap('+value.id+')"><i class="fa fa-print"></i></button></center></td>';
					tableData += '</tr>';
				}else{
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.order_no +'</td>';
					tableData += '<td>'+value.material_number+'<br>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.category +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					if ((result.admin == 'S-MIS') || (result.admin == 'S-PRD') || (result.admin == 'S-PCH') || (result.qa == 'PI0904005')) {
						tableData += '<td><center><button class="btn btn-danger btn-sm" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					}else{
						tableData += '<td>-</td>';
					}
					tableData += '<td><center><button class="btn btn-primary btn-sm" onclick="reprintScrap('+value.id+')"><i class="fa fa-print"></i></button></center></td>';	
					tableData += '</tr>';
				}

				// console.log(result.admin);
				
				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}

	function deleteScrap(id){

		if(confirm("Apa anda yakin akan menghapus slip scrap ini?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					var loc = $('#location').val();	

					fetchResume(loc);
					openSuccessGritter('Success!', result.message);
					// console.log(result);
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
		else{
			return false;
		}
	}

	function cancelScrap(id){

		if(confirm("Apa anda yakin akan mengcancel slip scrap ini?")){
			var data = {
				id:id
			}
			$.post('{{ url("cancel/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					var loc = $('#location').val();	

					fetchResume(loc);
					openSuccessGritter('Success!', result.message);
					// console.log(result);
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
		else{
			return false;
		}
	}

	function fetchScrapList(loc){
		if(loc == ""){
			return false;
		}

		fetchResume(loc);
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/scrap/list")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableData = '';
				$('#tableBodyList').html("");
				$('#tableBodyList').empty();
				
				var count = 1;
				$.each(result.lists, function(key, value) {
					var str = value.description;
					var desc = str.replace("'", "");
					tableData += '<tr onclick="fetchReturn(\''+value.material_number+'\''+','+'\''+desc+'\''+','+'\''+value.issue_location+'\''+','+'\''+value.spt+'\''+','+'\''+value.valcl+'\''+','+'\''+value.uom+'\''+','+'\''+value.remark+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ desc +'</td>';
					tableData += '<td>'+ value.uom +'</td>';
					tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '<td>'+ value.remark +'</td>';
					tableData += '</tr>';

					count += 1;
				});

				$('#tableBodyList').append(tableData);
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
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

				openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});

	}

	function TambahDefect(id, lop){
		var id = id;
		var lop = "";
		if (id == "div_tambah_defect"){
			lop = "lop";
		}else{
			lop = "lop2";
		}

		var isi = "";
		var bawah = "bawah";

		isi += "<div class='col-xs-12'>";

		isi += "<div class='col-xs-5' style='margin-bottom : 5px; padding-left: 0'><select class='form-control select2 select_defect' id='select_defect_"+no+"' name='select_defect' data-placeholder='Select Defect' style='width: 100%; height: 50px; font-size: 30px; text-align: center' required><option value=''>&nbsp;</option>@foreach($defect as $def)<option value='{{$def->defect}}''>{{$def->defect}}</option>@endforeach</select></div>";
		// isi += "<div class='col-xs-3' style='padding-left: 0'><input type='text' id='qty_defect_"+no+"' name='qty_defect' style='width: 100%; height: 35px; font-size: 20px; text-align: center' placeholder='QTY Defect'></div>";
		isi += "<div class='col-xs-3' style='padding-left: 0'><div class='input-group'><div class='input-group-btn'><button type='button' class='btn btn-danger' style='font-size: 20px; height: 35px; text-align: center;'  onclick='minusCount(\""+no+"\")'><span class='fa fa-minus'></span></button></div><input id='qty_defect_"+no+"' name='qty_defect_"+no+"' style='font-size: 20px; height: 35px; text-align: center;' type='number' class='form-control numpad trial' value='0' onchange='plusCount(\"0\")''><div class='input-group-btn'><button type='button' class='btn btn-success' style='font-size: 20px; height: 35px; text-align: center;' onclick='plusCount(\""+no+"\")'><span class='fa fa-plus'></span></button></div></div></div>";
		isi += "<div class='col-xs-2' style='padding-right: 10px'><button style='width: 100%' class='btn btn-success pull-right' type='button' onclick='TambahDefect(\""+id+"\",\""+lop+"\")'>Add Defect</button></div>";
		isi += "<div class='col-xs-2' style='padding-left: 0'><button style='width: 100%' class='btn btn-danger' type='button' onclick='KurangiDefect(this,\""+lop+"\", \""+no+"\")'>Remove Defect</button></div>";

		isi += "</div>";

		$("#"+id).append(isi);

		$("#qty_defect_"+no).numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		document.getElementById(lop).value = no;
		no+=1;

		$('.select2').select2({
			dropdownParent: $('#div_tambah_defect'),
			allowClear : true,
		});

		$('#kondisi').val(no);
	}

	function KurangiDefect(id,lop, nos){

		$(id).parent().parent().remove();

		// var kurang = $('#qty_defect_'+nos).val();
		// var qty = $('#quantity').val();


		// $('#quantity').val(parseInt(qty) - parseInt(kurang));

		// $('#qty_defect_'+no).remove();

		// var lop = lop;
		// var ids = $(id).parent('div').parent('div').attr('id');
		// var oldid = ids;

		// console.log(oldid);

		// $(id).parent('div').parent('div').remove();
		// var newid = parseInt(ids) + 1;
		// jQuery("#"+newid).attr("id",oldid);
		// jQuery("#divheader_"+newid).attr("id",oldid);
		// jQuery("#description"+newid).attr("name","description"+oldid);
		// jQuery("#header"+newid).attr("name","header"+oldid);
		// jQuery("#description"+newid).attr("id","description"+oldid);
		// jQuery("#header"+newid).attr("id","header"+oldid);
		// no-=1;
		// var a = no -1;

		// for (var i =  ids; i <= a; i++) { 
		// 	var newid = parseInt(i) + 1;
		// 	var oldid = newid - 1;
		// 	jQuery("#"+newid).attr("id",oldid);
		// 	jQuery("#description"+newid).attr("name","description"+oldid);
		// 	jQuery("#header"+newid).attr("name","header"+oldid);

		// 	jQuery("#description"+newid).attr("id","description"+oldid);
		// 	jQuery("#header"+newid).attr("id","header"+oldid);
		// }

		// document.getElementById(lop).value = a;
		// $('#kondisi').val(a);		
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