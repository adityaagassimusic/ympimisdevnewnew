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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="id_team" value="{{ $id_team }}">
</section>

<div class="modal fade" id="modalLocation">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a;">Pilih Periode KY</h3></center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<select id="select-periode" class="form-control select2" onchange="SelectPeriode(value)" data-placeholder="Pilih Periode KY ..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($list_soal as $list_soal)
							<option value="{{ $list_soal->kode_soal }}/{{ $list_soal->periode }}">Periode {{ $list_soal->bulan }} - ({{ $list_soal->remark }})</option>
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
	
		$('.select2').select2();
		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

	});

	function SelectPeriode(){
		var periode = $('#select-periode').val();
		var p = periode.split("/");
		var id_team = $('#id_team').val();
		var data = {
			periode : p[0],
			id_team : id_team
		}

		$.post('{{ url("update/kode_soal/input") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success!', result.message);
				window.location.href = '{{ url('index/soal/ky') }}/'+id_team+'/'+p[1]+'';
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>