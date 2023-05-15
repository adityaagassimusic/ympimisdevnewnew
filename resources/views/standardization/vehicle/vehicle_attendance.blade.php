@extends('layouts.master')
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
	color: black;
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		Absensi Pemeriksaan Kendaraan<span class="text-purple"> </span>
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
				<div class="col-md-12" style="padding-right:0">
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
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="col-md-12" style="padding-right: 2px;">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #33d6ff;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;">
							<span style="font-size: 25px;text-align: center;font-weight: bold;">
								LIST ATTENDANCE 
							</span>
					</center>
						<div style="width: 100%;height: 100%;vertical-align: middle;padding-top: 20px">
							<div class="col-xs-12">
								<div class="row">
									<center style="background-color: #33ff92;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NAMA</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="name"></span>
									</center>
								</div>
							</div>
							<div class="col-xs-12">
								<div class="row">
									<center style="background-color: lightsalmon;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">NOMOR POLISI</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="nopol_all"></span>
									</center>
								</div>
							</div>
							<!-- <div class="col-xs-4">
								<div class="row">
									<center style="background-color: #33ff92;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">Jumlah Kendaraan</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="jumlah_kendaraan"></span>
									</center>
								</div>
							</div> -->
							<!-- <div class="col-xs-12">
								<div class="row">
									<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">Nomor Polisi</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 50px;font-weight: bold;" id="nopol"></span>
									</center>
								</div>
							</div> -->
							<!-- <div class="col-xs-12">
								<div class="row">
									<center style="background-color: orange;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;"><span style="font-size: 15px;text-align: center;font-weight: bold;">Destinasi</span></center>
									<center style="border: 1px solid black">
										<span style="font-size: 30px;font-weight: bold;" id="destinasi"></span>
									</center>
								</div>
							</div> -->
						</div>
						<div class="col-xs-12">
							<div class="row">
								<table id="tableAttendance" class="table table-bordered table-striped table-hover">
									<thead style="background-color: rgb(126,86,134);" id="headTableAttendance">
										<tr>
											<th style="color:white;width:1%">ID</th>
											<th style="color:white;width:1%">Employee ID</th>
											<th style="color:white;width:4%">Nama</th>
											<th style="color:white;width:2%">Nomor Polisi</th>
											<th style="color:white;width:2%">Keterangan</th>
											<th style="color:white;width:2%">Waktu Pengambilan</th>
										</tr>
									</thead>
									<tbody id="bodyTableAttendance">
									</tbody>
									<tfoot id="footTableAttendance">
										<tr>
											<th></th>
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

		$('.select3').select2({
			dropdownAutoWidth : true,
			dropdownParent: $("#create_modal"),
			allowClear:true,
		});
		
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
				
				$.get('{{ url("fetch/standardization/vehicle_attedance") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Scan Berhasil');
						$('#loading').hide();
						$('#name').html(result.emp[0].name);
						if (result.emp.length == 1) {
							$('#nopol_all').html(result.emp[0].nopol);
						} else if (result.emp.length == 2){
							$('#nopol_all').html(result.emp[0].nopol+' , '+result.emp[1].nopol);
						}
						audio_ok.play();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
						$('#tag').focus();
						fetchQueue();
					}else{
						$('#loading').hide();
						$('#tag').removeAttr('disabled');
						$('#tag').val("");
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
				$('#tag').focus();
				fetchQueue();
				audio_error.play();
				openErrorGritter('Error!','Masukkan ID Card Dengan Benar');
			}
		}
	});

	function fetchHeadFoot() {
		$('#headTableAttendance').html('');
		var headTable = '';
		$('#footTableAttendance').html('');
		var footTable = '';

		headTable += '<tr>';
		headTable += '<th style="color:white;width:1%">ID</th>';
		headTable += '<th style="color:white;width:1%">Employee ID</th>';
		headTable += '<th style="color:white;width:4%">Nama</th>';
		headTable += '<th style="color:white;width:2%">Nomor Polisi</th>';
		// headTable += '<th style="color:white;width:7%">Destinasi</th>';
		// headTable += '<th style="color:white;width:1%">Jumlah</th>';
		headTable += '<th style="color:white;width:2%">Keterangan</th>';
		headTable += '<th style="color:white;width:2%">Waktu Pengambilan</th>';
		headTable += '</tr>';

		footTable += '<tr>';
		footTable += '<th></th>';
		footTable += '<th></th>';
		footTable += '<th></th>';
		// footTable += '<th></th>';
		// footTable += '<th></th>';
		// footTable += '<th></th>';
		footTable += '<th></th>';
		footTable += '<th></th>';
		footTable += '<th></th>';
		footTable += '</tr>';

		$('#headTableAttendance').append(headTable);
		$('#footTableAttendance').append(footTable);
	}

	function fetchQueue() {
		$.get('{{ url("fetch/standardization/attendance_queue") }}',  function(result, status, xhr){
			if(result.status){
				fetchHeadFoot();
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
					if (result.emp[i].nopol_2 == null) {
						datas += '<td>'+result.emp[i].nopol+'</td>';
					}else{

						datas += '<td>'+result.emp[i].nopol+', '+result.emp[i].nopol_2+'</td>';
					}

					if (result.emp[i].attend_date_pemeriksaan == null) {
						datas += '<td style="background-color:#ff9999"></td>';
					}
					else{
						datas += '<td style="background-color:#99ffa2">Sudah Mengambil</td>';
					}

					if (result.emp[i].attend_date_pemeriksaan == null) {
						datas += '<td style="background-color:#ff9999"></td>';
					}else{
						datas += '<td style="background-color:#99ffa2">'+result.emp[i].attend_date_pemeriksaan+'</td>';
					}
					datas += '</tr>';

					index++;
				}

				$('#bodyTableAttendance').append(datas);

				$('#tableAttendance tfoot th').each( function () {
			        var title = $(this).text();
			        $(this).html( '<input style="text-align: center;width:100%" type="text" placeholder="Search '+title+'"/>' );
			      } );
			      var table = $('#tableAttendance').DataTable({
			        "order": [],
			        'dom': 'Bfrtip',
			        'responsive': true,
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
			        }
			      });

			      table.columns().every( function () {
			        var that = this;

			        $( 'input', this.footer() ).on( 'keyup change', function () {
			          if ( that.search() !== this.value ) {
			            that
			            .search( this.value )
			            .draw();
			          }
			        } );
			      } );

			      $('#tableAttendance tfoot tr').appendTo('#tableAttendance thead');
		      
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