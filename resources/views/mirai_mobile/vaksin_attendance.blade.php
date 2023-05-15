@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
	vertical-align: middle;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
	vertical-align: middle;
}
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
	padding: 1px;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
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
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait... <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-md-6">
					<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
						<div class="input-group input-group-lg">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
								<i class="fa fa-credit-card-alt"></i>
							</div>
							<input type="text" class="form-control" style="text-align: center;" placeholder="SCAN RFID" id="tag">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
								<i class="fa fa-credit-card-alt"></i>
							</div>
						</div>
					</div>
				</div><div class="col-md-6">
					<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
						<div class="form-group">
							<select class="form-control select2" id="" data-placeholder='Data Karyawan dan Keluarga' style="width: 100%" onchange="selectPerson(this)">
				              <option value="">&nbsp;</option>
				              @foreach($attend as $att)
				              <option value="{{$att->employee_id}}_{{$att->hubungan_keluarga}}">{{$att->employee_id}} - {{$att->name}}</option>
				              @endforeach
				            </select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="col-md-12" style="padding-right: 2px;">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #33d6ff;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 25px;text-align: center;font-weight: bold;">DATA PESERTA</span></center>
						<div style="width: 100%;height: 100%;vertical-align: middle;padding-top: 20px">
							<div class="col-xs-6">
								<div class="row">
									<center style="background-color: #33ff92;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAMA</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="nama"></span>
									</center>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="row">
									<center style="background-color: #33ff92;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">HUBUNGAN KELUARGA</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="hubungan_keluarga"></span>
									</center>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="row">
									<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NO KTP</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="no_ktp"></span>
									</center>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="row">
									<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NO HP</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="no_hp"></span>
									</center>
								</div>
							</div>

						</div>
						<div class="col-xs-12">
							<div class="row">
								<table id="tableAttendance" class="table table-bordered table-hover">
									<thead style="background-color: rgb(126,86,134); color: #fff;">
										<tr>
											<th width="1%">ID</th>
											<th width="1%">Employee ID</th>
											<th width="3%">Name</th>
											<th width="1%">Relation</th>
											<th width="2%">KTP</th>
											<th width="2%">Alamat</th>
											<th width="1%">Keterangan</th>
											<th width="2%">Time At</th>
										</tr>
									</thead>
									<tbody id="bodyTableAttendance">
									</tbody>
								</table>
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

	jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		clearAll();
		fetchQueue();
	});

	function clearAll() {
		$('#tag').removeAttr('disabled');
		$('#tag').val("");
		$('#tag').focus();
	}

	$('#tag').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			if($("#tag").val().length >= 7){
				var data = {
					tag : $("#tag").val(),
				}
				
				$.get('{{ url("fetch/miraimobile/vaksin_attendance") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Scan Berhasil');
						$('#loading').hide();
						$('#nama').html(result.emp.name);
						$('#hubungan_keluarga').html(result.emp.hubungan_keluarga);
						$('#no_ktp').html(result.emp.no_ktp);
						$('#no_hp').html(result.emp.phone);
						audio_ok.play();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
						$('#name').html("");
						$('#tag').focus();
						fetchQueue();
					}else{
						$('#loading').hide();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
						$('#name').html("");
						$('#tag').focus();
						fetchQueue();
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				})
			}else{
				$('#loading').hide();
				$('#tag').removeAttr('disabled');
				$('#tag').val("");
				$('#name').html("");
				$('#tag').focus();
				fetchQueue();
				audio_error.play();
				openErrorGritter('Error!','Masukkan ID Card Dengan Benar');
			}
		}
	});

	function selectPerson(elem) {
		$('#loading').show();
		var data = {
			tag : elem.value,
		}
		
		$.get('{{ url("fetch/miraimobile/vaksin_attendance_select") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Scan Berhasil');
				$('#loading').hide();
				$('#nama').html(result.emp[0].name);
				$('#hubungan_keluarga').html(result.emp[0].hubungan_keluarga);
				$('#no_ktp').html(result.emp[0].no_ktp);
				$('#no_hp').html(result.emp[0].phone);
				audio_ok.play();
				fetchQueue();
			}else{
				$('#loading').hide();
				audio_error.play();
				$('#name').html("");
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function fetchQueue() {
		$.get('{{ url("fetch/miraimobile/vaksin_attendance/queue") }}',  function(result, status, xhr){
			if(result.status){
				$('#tableAttendance').DataTable().clear();
				$('#tableAttendance').DataTable().destroy();
				var datas = '';
				$('#bodyTableAttendance').html('');
				var index = 1;
				for(var i = 0; i < result.emp.length; i++){
					datas += '<tr>';
					datas += '<td>'+index+'</td>';
					datas += '<td>'+result.emp[i].employee_id+'</td>';
					datas += '<td>'+result.emp[i].name+'</td>';
					datas += '<td>'+result.emp[i].hubungan_keluarga+'</td>';
					datas += '<td>'+result.emp[i].no_ktp+'</td>';
					datas += '<td>'+result.emp[i].address+'</td>';

					if (result.emp[i].status == null) {
						datas += '<td style="background-color:#ff9999"></td>';
					}else{
						datas += '<td style="background-color:#99ffa2">'+result.emp[i].status.split('_')[0]+'</td>';
					}
					// datas += '<td>'+(result.emp[i].keterangan || '')+'</td>';
					if (result.emp[i].status == null) {
						datas += '<td style="background-color:#ff9999"></td>';
					}else{
						datas += '<td style="background-color:#99ffa2">'+result.emp[i].status.split('_')[1]+'</td>';
					}
					datas += '</tr>';
					index++;
				}
				$('#bodyTableAttendance').append(datas);

				var table = $('#tableAttendance').DataTable({
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
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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