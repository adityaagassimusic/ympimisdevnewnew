@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
	}
	th:hover {
		overflow: visible;
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
	html {
	  scroll-behavior: smooth;
	}


	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}

	#tableCode > tbody > tr > td:hover{
		background-color: #7dfa8c !important;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
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
	<div class="row" style="padding-left: 10px;padding-right: 10px;">
		<div class="col-xs-6" style="padding-bottom: 10px;text-align: center;padding-left: 0px;padding-right: 5px;">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Assessor ID (アセッサーID​​​)</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 5%;font-size: 15px">Assessor Name (アセッサー名)</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="assessor_id">{{$emp->employee_id}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="assessor_name">{{$emp->name}}</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-6" style="padding-bottom: 10px;text-align: center;padding-left: 5px;padding-right: 0px;">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)">
				<tr>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Periode (期間)</td>
					<td style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px">Proses (処理)</td>
				</tr>
				<tr>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px" id="periode">{{$periode}}</td>
					<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px"><span id="process"></span> (<span id="process_jp"></span>)</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" style="padding-bottom: 10px;text-align: center;padding-left: 0px;padding-right: 0px;">
			<table style="width: 100%;border:2px solid rgb(60, 60, 60)" id="tableAssessment">
				<thead>
					<tr>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 1%;font-size: 15px;text-align: center;">#</th>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 3%;font-size: 15px;text-align: center;">Emp (従業員)</th>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 6%;font-size: 15px;text-align: center;">Slogan (スローガン)</th>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 2%;font-size: 15px;text-align: center;">Checklist (チェックリスト)</th>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 4%;font-size: 15px;text-align: center;">Point Penilaian (採点項目)</th>
						<th style="background-color: #c7c7c7;font-weight: bold;border:2px solid rgb(60, 60, 60);width: 4%;font-size: 15px;text-align: center;">Nilai (得点)</th>
					</tr>
				</thead>
				<tbody id="bodyAssessment">
					
				</tbody>
			</table>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr_sudah = null;
	var arr_belum = null;
	var kataconfirm = 'Apakah Anda yakin?';

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillData();

		$('.select2').select2({
			allowClear:true
		});
	});

	function fillData() {
		$("#loading").show();
		var data = {
			periode:'{{$periode}}',
			assessor_id:'{{$emp->employee_id}}'
		}
		$.get('{{ url("fetch/slogan/assessment") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableAssessment').DataTable().clear();
        		$('#tableAssessment').DataTable().destroy();
				$('#process').html(result.process)
				if (result.process == 'Seleksi') {
					$('#process_jp').html('選抜');
				}else{
					$('#process_jp').html('決勝');
				}
				$('#bodyAssessment').html('');
				var bodyAssessment = '';
				for(var i = 0; i < result.slogan.length;i++){
					bodyAssessment += '<tr id="tr_'+i+'">';
					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align:right;padding-right:7px;">'+(i+1)+'</td>';
					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align:left;padding-left:7px;">'+result.slogan[i].employee_id+' - '+result.slogan[i].name+'</td>';
					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align:left;padding-left:7px;">'+result.slogan[i].slogan_1+'</td>';
					var ok = 'OK';
					var ng = 'NG';
					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;"><button class="btn btn-success btn-lg" onclick="checkList(\''+ok+'\',\''+i+'\',\''+result.slogan[i].id+'\')">OK</button><button class="btn btn-danger btn-lg" style="margin-left:10px;" onclick="checkList(\''+ng+'\',\''+i+'\',\''+result.slogan[i].id+'\')">NG</button></td>';
					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align:left;padding-left:7px;">';
					bodyAssessment += '<div id="divPoint_'+i+'" style="display:none;">';
					bodyAssessment += '<b>Point Penilaian (採点項目)</b><br>';
					bodyAssessment += '1. Kesesuaian Pesan / isi yang disampaikan dengan implementasi tindakan konkret <b>(Max. 50)</b> (内容（最大50点）)<br>';
					bodyAssessment += '2. Originalitas <b>(Max. 35)</b> (独創性（最大35点）)<br>';
					bodyAssessment += '3. Sifat Persuasif Program <b>(Max. 15)</b> 説得力（最大15点）<br>';
					bodyAssessment += '</div>';
					bodyAssessment += '</td>';

					bodyAssessment += '<td  style="background-color:#fff;color:#000;border:2px solid rgb(60, 60, 60);font-size: 18px;text-align:left;padding-left:7px;">';
					bodyAssessment += '<div id="divNilai_'+i+'" style="display:none;">';
					bodyAssessment += '<b>Hasil Penilaian</b><br>';
					bodyAssessment += '<input style="width:100%;border-bottom:2px solid black;font-size:20px;" id="result_1_'+i+'" placeholder="Nilai 1 (採点１)" class="form-control numpad" onchange="checkNilai(this.value,this.id)"><br>';
					bodyAssessment += '<input style="width:100%;border-bottom:2px solid black;font-size:20px;" id="result_2_'+i+'" placeholder="Nilai 2 (採点２)" class="form-control numpad" onchange="checkNilai(this.value,this.id)"><br>';
					bodyAssessment += '<input style="width:100%;border-bottom:2px solid black;font-size:20px;" id="result_3_'+i+'" placeholder="Nilai 3 (採点３)" class="form-control numpad" onchange="checkNilai(this.value,this.id)"><br>';
					bodyAssessment += '<button class="btn btn-danger pull-left" onclick="cancelNilai(\''+i+'\',\''+result.slogan[i].id+'\')">Cancel (キャンセル)</button>';
					bodyAssessment += '<button class="btn btn-success pull-right" onclick="submitNilai(\''+i+'\',\''+result.slogan[i].id+'\')">Submit (提出)</button>';
					bodyAssessment += '</div>';
					bodyAssessment += '</td>';

					bodyAssessment += '</tr>';
				}
				$('#bodyAssessment').append(bodyAssessment);

				$('.numpad').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				if (result.assessor == null) {
					alert('Anda Tidak Memiliki Hak Akses');
					location.replace("{{url('index/slogan')}}");
				}
				$("#loading").hide();
			}else{
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		});
	}

	function cancelNilai(index,id) {
		$('#divNilai_'+index).hide();
		$('#divPoint_'+index).hide();
	}

	function submitNilai(index,id) {
		$("#loading").show();
		var result_1 = $('#result_1_'+index).val();
		var result_2 = $('#result_2_'+index).val();
		var result_3 = $('#result_3_'+index).val();

		if (result_1 == '' || result_2 == '' || result_3 == '') {
			openErrorGritter('Error!','Isi semua nilai.');
		}
		var data = {
			id:id,
			cond:'OK',
			result_1:result_1,
			result_2:result_2,
			result_3:result_3,
			process:$('#process').text(),
		}

		$.post('{{ url("input/slogan/assessment") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#tr_'+index).remove();
				openSuccessGritter('Success','Success Input Nilai');
			}else{
				$("#loading").hide();
				audio_error.play()
				openErrorGritter('Error!',result.message);
				return false;
			}
		})
	}

	function checkNilai(value,id) {
		if (id.match(/result_1/gi)) {
			if (parseFloat(value) > 50) {
				openErrorGritter('Error!','Maksimal Nilai : 50');
				$("#"+id).val('');
			}
		}
		if (id.match(/result_2/gi)) {
			if (parseFloat(value) > 35) {
				openErrorGritter('Error!','Maksimal Nilai : 35');
				$("#"+id).val('');
			}
		}
		if (id.match(/result_3/gi)) {
			if (parseFloat(value) > 15) {
				openErrorGritter('Error!','Maksimal Nilai : 15');
				$("#"+id).val('');
			}
		}
	}

	function checkList(cond,index,id) {
		if (cond == 'OK') {
			$('#divNilai_'+index).show();
			$('#divPoint_'+index).show();
		}else{
			$("#loading").show();
			var data = {
				id:id,
				cond:cond,
				result_1:'0',
				result_2:'0',
				result_3:'0',
				process:$('#process').text(),
			}

			$.post('{{ url("input/slogan/assessment") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#tr_'+index).remove();
					openSuccessGritter('Success','Success Input Nilai');
				}else{
					$("#loading").hide();
					audio_error.play()
					openErrorGritter('Error!',result.message);
					return false;
				}
			})
		}
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




</script>
@endsection