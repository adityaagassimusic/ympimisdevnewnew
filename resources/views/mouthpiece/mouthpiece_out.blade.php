@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.status {
		font-size: 3vw;
		font-weight: bold;
		text-transform: uppercase;
	}

	#tableDetail>tbody>tr:hover {
		background-color: #7dfa8c !important;
	}

	tbody>tr>td {
		padding: 10px 5px 10px 5px;
	}

	table.table-bordered {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>thead>tr>th {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>tbody>tr>td {
		border: 1px solid black;
		background-color: aliceblue;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
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
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<table class="table table-bordered table-hover" id="tableDetail" width="100%">
									<thead style="background-color: #9f7fb0; color: white;">
										<tr>
											<th style="width: 5%;">#</th>
											<th style="width: 15%;">GMC</th>
											<th style="width: 30%;">MATERIAL DESCRIPTION</th>
											<th style="width: 10%;">TO LOCATION</th>
											<th style="width: 10%;">QTY</th>
											<th style="width: 15%;">REQUEST BY</th>
											<th style="width: 15%;">#</th>
										</tr>
									</thead>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</tfoot>
									<tbody id="tableBodyResume"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalConfirmation">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<div style="background-color: #00c0ef;">
							<center>
								<h3>Scan Print Slip Request</h3>
							</center>
						</div>
						<label>Scan Slip Request</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="slip_request" onchange="ScanSlip(this.value)" placeholder="Scan In Here" required>
						<br><br>
						<a href="{{ url("/index/mouthpiece_process") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-hand-o-right"></i> Ke Halaman Utama</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="ModalNotification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="alrm-notif">
					<h4 style="padding-left: 10px; padding-top: 10px" id="title-notif"></h4>
					<p style="padding-left: 10px; padding-top: 10px">Berikut adalah list permintaan Mouthpiece dari Assemby :<br>
						<label style="color: black">(Klik tombol konfirmasi untuk membuka detail.)</label>
					</p>
				</div>   
			</div>
			<div class="modal-footer">
				<table class="table table-bordered table-stripped" id="tableListRequest" style="width: 100%;" align="center">
					<thead style="background-color: rgb(126,86,134); color: #FFD700;">
						<tr>
							<th style="width: 5%;">#</th>
							<th style="width: 15%;">GMC</th>
							<th style="width: 30%;">MATERIAL DESCRIPTION</th>
							<th style="width: 10%;">TO LOCATION</th>
							<th style="width: 10%;">QTY</th>
							<th style="width: 10%;">REQUEST BY</th>
							<th style="width: 10%;">STATUS PACKING</th>
							<th style="width: 10%;">#</th>
						</tr>                   
					</thead>
					<tbody id="tableBodyListRequest" style="background-color: white">
					</tbody>
				</table>
				<div class="col-xs-12">
					<div class="col-xs-4 pull-left">
						<button type="button" class="btn btn-warning" data-dismiss="modal" style="font-weight: bold; font-size: 1vw; width: 100%;"> <i class="fa fa-times" aria-hidden="true"></i> CLOSE</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_clock_lobby = new Audio('{{ url("sounds/railway_lobby.mp3") }}');
	var i = 0;

	jQuery(document).ready(function() {
		// clearAll();
		// $('#finishPicking').prop('disabled', true);
		// $('#modalOperator').modal({
		// 	backdrop: 'static',
		// 	keyboard: false
		// });

		// $('#modalOperator').on('shown.bs.modal', function () {
		// 	$('#operator').focus();
		// });
		TableList();
		setInterval(RequestAssy, 5000);
		setInterval(function() {
			i++;
			if(i%2 == 0){
				$(".alrm-notif").css("background-color", "red");
				$(".alrm-notif").css("color", "white");
			} else {
				$(".alrm-notif").css("background-color", "white");
				$(".alrm-notif").css("color", "red");
			}
		}, 1000);
	});

	function TableList(){
		$.get('{{ url("fetch/request/mouthpiece/assy") }}', function(result, status, xhr){
			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();
			var tableData = '';
			$('#tableBodyResume').html("");
			$('#tableBodyResume').empty();

			var count = 1;

			$.each(result.resume, function(key, value) {

				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td style=" text-align: center">'+ value.gmc +'</td>';	
				tableData += '<td style=" text-align: center">'+ value.desc +'</td>';	
				tableData += '<td style=" text-align: center">'+ value.issue +'</td>';	
				tableData += '<td style=" text-align: center">'+ value.qty +' '+ value.uom +'</td>';	
				tableData += '<td style=" text-align: center">'+ value.created_by +'</td>';	
				tableData += '<td style=" text-align: center">'+ value.created_at +'</td>';	
				tableData += '</tr>';

				count += 1;

			});

			$('#tableBodyResume').append(tableData);

			$('#tableDetail tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
			} );

			var table = $('#tableDetail').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
					[ 5, 10, 25, -1 ],
					[ '5 rows', '10 rows', '25 rows', 'Show all' ]
					],
				'buttons': {
					buttons: [{
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
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				'ordering' :true,
				'pageLength': 20,
				initComplete: function() {
					this.api()
					.columns([1, 3])
					.every(function(dd) {
						var column = this;
						var theadname = $("#tableDetail th").eq([dd]).text();
						var select = $(
							'<select style="width: 100%; color: black;"><option value="" style="font-size:11px;">All</option></select>'
							)
						.appendTo($(column.footer()).empty())
						.on('change', function() {
							var val = $.fn.dataTable.util.escapeRegex($(this).val());

							column.search(val ? '^' + val + '$' : '', true, false)
							.draw();
						});
						column
						.data()
						.unique()
						.sort()
						.each(function(d, j) {
							var vals = d;
							if ($("#tableDetail th").eq([dd]).text() == 'Category') {
								vals = d.split(' ')[0];
							}
							select.append('<option style="font-size:11px;" value="' +
								d + '">' + vals + '</option>');
						});
					});
				},
			});

			table.columns().every( function () {
				var that = this;
				$( '#search', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
						.search( this.value )
						.draw();
					}
				} );
			} );

			$('#tableDetail tfoot tr').appendTo('#tableDetail thead');
		});
	}

	function clearAll(){
		$('#picking').hide();
		$('#employee_id').val('');
		$('#kd_number').val('');
		$('#operator').val('');
		$('#qr_item').val('');
		$('#qr_checksheet').val('');

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#qr_item').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#qr_item').val().length == 7){
				$('#loading').show();
				var material_number = $('#qr_item').val();
				var kd_number = $('#kd_number').val();
				var employee_id = $('#employee_id').val();
				var data = {
					kd_number:kd_number,
					material_number:material_number,
					employee_id:employee_id
				}
				$.post('{{ url("scan/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
					if(result.status){
						$('#qr_item').val("");
						$('#qr_item').focus();
						selectChecksheet(kd_number);
						$('#loading').hide();
						openSuccessGritter('Success', result.message);
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#qr_item').val('');							
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Kode material tidak valid');
				$('#qr_item').val('');			
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/kd_mouthpiece/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#data_op').text(" ("+result.employee.employee_id+" - "+result.employee.name+")")
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_checksheet').val('');
						$('#qr_item').val('');
						$('#qr_checksheet').focus();
						// $('#modalChecksheet').modal('show');

						// $('#modalChecksheet').modal({
						// 	backdrop: 'static',
						// 	keyboard: false
						// });

						// $('#modalChecksheet').on('shown.bs.modal', function () {
						// 	$('#checksheet').focus();
						// });
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

	function focusTag(){
		$('#qr_item').focus();
	}

	function finishPicking(){
		$('#loading').show();
		var kd_number = $('#kd_number').val();
		var data = {
			kd_number:kd_number
		}
		if(confirm("Apakah anda yakin picking sudah selesai?")){
			$.post('{{ url("create/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
				if(result.status){
					location.reload(true);
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();				
				}
			});
		}
		else{
			return false;
		}
	}

	function selectChecksheet(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.get('{{ url("fetch/kd_mouthpiece/picking") }}', data, function(result, status, xhr){
			if(result.status){
				$('#checksheet').hide();
				$('#picking').show();
				$('#pickingTableBody').html("");

				$('#kd_number').val(id);

				var pickingData = "";

				var total_quantity = 0;
				var total_actual = 0;

				$.each(result.checksheet_details, function(key, value){
					pickingData += '<tr>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+(key+1)+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.remark.toUpperCase()+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_number+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_description+'</td>';
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.quantity+'</td>';
					if(value.quantity > value.actual_quantity){
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">-</td>';
					}
					else{
						pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';						
					}
					pickingData += '</tr>';

					total_quantity += value.quantity;
					total_actual += value.actual_quantity;
				});

				if(total_quantity == total_actual){
					$('#finishPicking').prop('disabled', false);
				}

				$('#pickingTableBody').append(pickingData);
				setInterval(focusTag, 1000);
				$('#loading').hide();
			}
			else{
				$('#qr_checksheet').val("");
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$('#qr_checksheet').focus();
			}
		});

	}

	$('#qr_checksheet').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_checksheet").val().length == 10){
				selectChecksheet($("#qr_checksheet").val());
			}
			else{
				openErrorGritter('Error!', 'QR Checksheet tidak valid.');
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

	function RequestAssy(){
		$.get('{{ url("fetch/request/mouthpiece/assy") }}', function(result, status, xhr) {
			if(result.status){
				$("#title-notif").html('<i class="fa fa-bell-o" aria-hidden="true"></i> Permintaan Mouthpiece !');
				$("#ModalNotification").modal('show');

				var ada = result.cek_request;

				if (ada.length > 0) {
					audio_clock_lobby.play();
				}

				$('#tableListRequest').DataTable().clear();
				$('#tableListRequest').DataTable().destroy();
				var tableData = '';
				$('#tableBodyListRequest').html("");
				$('#tableBodyListRequest').empty();

				var count = 1;
				$.each(result.request, function(key, value) {
					var employee = value.created_by.split("/");

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ count +'</td>';
					tableData += '<td style="text-align: center">'+ value.gmc +'</td>';
					tableData += '<td style="text-align: center">'+ value.desc +'</td>';
					tableData += '<td style="text-align: center">'+ value.issue +'</td>';
					tableData += '<td style="text-align: center">'+ value.qty +' '+ value.uom +'</td>';
					tableData += '<td style="text-align: center">'+ employee[1] +'</td>';
					// tableData += '<td style="text-align: center"><button type="button" class="btn btn-info" onClick="KonfirmasiRequest()"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Konfirmasi</button></td>';
					if (value.packing == 'onprogress'){
						tableData += '<td style="text-align: center"><button type="button" class="btn btn-warning btn-xs" onClick="SelesaiPacking(\''+value.request_id+'\')" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Klik Selesai Packing</button>';

						tableData += '<td style="text-align: center">-</td>';
					}else if (value.packing == 'finished'){
						tableData += '<td style="text-align: center"><span class="label label-success" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Process Packing Selesai</span>';

						tableData += '<td style="text-align: center"><a target="_blank" href="{{ url("index/detail/request") }}/'+value.request_id+'" class="btn btn-info btn-xs" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Konfirmasi</a></td>';
					}else{
						tableData += '<td style="text-align: center"><button type="button" class="btn btn-danger btn-xs" style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;" onClick="KonfirmasiPacking(\''+value.request_id+'\')"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Klik Untuk Packing</button>';

						tableData += '<td style="text-align: center">-</td>';
					}
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyListRequest').append(tableData);
				TableList();
			}else{
				$("#ModalNotification").modal('hide');
			}
		});
	}

	function KonfirmasiPacking(request_id){
		var data = {
			request_id:request_id
		}
		$.post('{{ url("update/persiapan/request") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', 'Pesanan Dalam Prosess Packing');
				RequestAssy();
			}
			else{
				openErrorGritter('Error', 'Pesanan Gagal');
			}
		});
	}

	function SelesaiPacking(request_id){
		var data = {
			request_id:request_id
		}
		$.post('{{ url("update/done/request") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', 'Pesanan Telah Siap Diambil');
				RequestAssy();
			}
			else{
				openErrorGritter('Error', 'Pesanan Gagal Disiapkan');
			}
		});
	}

	SelesaiPacking

	function KonfirmasiRequest(){
		$("#ModalNotification").modal('hide');
		$("#modalConfirmation").modal('show');
		$('#slip_request').focus();
	}

	function ScanSlip(value){
		var data = {
			value:value
		}
		$.post('{{ url("update/request/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', 'Berhasil menambah stock, Material ter CS secara otomatis.');
				// audio_ok.play();
				$("#modalConfirmation").modal('hide');
				RequestAssy();
				TableList();
			}
			else{
				$('#loading').hide();
				// audio_error.play();
				openErrorGritter('Error', 'Gagal Menyimpan, Pastikan Kanban Sesuai.	');
			}
		});
	}


</script>
@endsection

