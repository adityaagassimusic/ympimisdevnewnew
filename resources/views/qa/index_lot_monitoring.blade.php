@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
		color: black;
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	.content-wrapper{
		color: white;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
	    width: 180px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }

	 .dataTables_info{
	 	color: black;
	 	text-align: left;
	 }

	 .dataTables_filter{
	 	color: black;
	 }
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px">
			<div class="col-xs-4" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;">
				<span style="font-size: 20px;color: white;width: 100%;font-weight: bold;" id="periode"></span>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;padding-right: 5px">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;text-align: left">
				<!-- <div class="input-group"> -->
					<select id="lot_status" style="width: 100%;text-align: left;height:30px;" class="form-control select2" data-placeholder="Pilih Lot Status">
						<option value=""></option>
						<option value="Lot OK">Lot OK</option>
						<option value="Lot Out">Lot Out</option>
					</select>
				<!-- </div> -->
			</div>
			<div class="col-xs-1" style="padding-left: 5px;padding-right: 5px;">
				<button class="btn btn-default pull-left" onclick="fetchLotStatus()" style="font-weight: bold;height:35px;background-color: rgb(126,86,134);color: white;border:1px solid rgb(126,86,134)">
					Search
				</button>
			</div>
			<!-- <div class="col-xs-1" style="padding-left: 0px">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 10px"></div>
			</div> -->
		</div>
		<div class="gambar" style="margin-top:0px" id="container_WI">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px;font-weight: bold;">Incoming WI
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_WI"><span id="lot_ok_WI">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_WI"><span id="lot_out_WI">0</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="gambar" style="margin-top:0px" id="container_EI">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px;font-weight: bold;">Incoming EI
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_EI"><span id="lot_ok_EI">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_EI"><span id="lot_out_EI">0</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="gambar" style="margin-top:0px" id="container_TRUE">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px;font-weight: bold;">Outgoing TRUE
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_TRUE"><span id="lot_ok_TRUE">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_TRUE"><span id="lot_out_TRUE">0</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="gambar" style="margin-top:0px" id="container_KBI">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px;font-weight: bold;">Outgoing KBI
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_KBI"><span id="lot_ok_KBI">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_KBI"><span id="lot_out_KBI">0</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="gambar" style="margin-top:0px" id="container_ARISA">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px;font-weight: bold;">Outgoing ARISA
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;font-weight: bold;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_ARISA"><span id="lot_ok_ARISA">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_ARISA"><span id="lot_out_ARISA">0</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
			<div class="box-body">
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
						<thead style="background-color: rgb(126,86,134);text-align: left;">
							<tr>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Item Desc.</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Qty Check (Pcs)</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 6%">Defect</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Repair</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Return</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Ratio (%)</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Vendor</th>
								<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Status</th>
							</tr>
						</thead>
						<tbody id="body_table_lot" style="text-align:center;">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')

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

	jQuery(document).ready(function(){
		$('.select2').select2({
			allowClear:true
		});
		fetchLotStatus();
		setInterval(fetchLotStatus, 600000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		$('#loading').show();
		// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			lot_status:$('#lot_status').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/lot_status") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					// $.each(result.lot_count, function(key,value){
						// $('#lot_ok_'+value.location).html(value.lot_ok);
						// $('#lot_out_'+value.location).html(value.lot_out);

						// if (parseInt(value.lot_ok) > 0) {
						// 	$('#lot_ok_td_'+value.location).css("background-color","rgb(0, 166, 90)",'important');
						// 	$('#lot_ok_td_'+value.location).css("color","white",'important');
						// }else{
						// }

						// $('#lot_ok_td_WI').css("background-color","white",'important');
						// $('#lot_ok_td_WI').css("color","black",'important');
						// $('#lot_ok_td_WI').css("font-weight","bold",'important');

						// $('#lot_ok_td_EI').css("background-color","white",'important');
						// $('#lot_ok_td_EI').css("color","black",'important');
						// $('#lot_ok_td_EI').css("font-weight","bold",'important');

						$('#lot_ok_td_TRUE').css("background-color","white",'important');
						$('#lot_ok_td_TRUE').css("color","black",'important');
						$('#lot_ok_td_TRUE').css("font-weight","bold",'important');

						$('#lot_ok_td_KBI').css("background-color","white",'important');
						$('#lot_ok_td_KBI').css("color","black",'important');
						$('#lot_ok_td_KBI').css("font-weight","bold",'important');

						$('#lot_ok_td_ARISA').css("background-color","white",'important');
						$('#lot_ok_td_ARISA').css("color","black",'important');
						$('#lot_ok_td_ARISA').css("font-weight","bold",'important');

						// if (parseInt(value.lot_out) > 0) {
							// $('#lot_out_td_'+value.location).css("background-color","#dd4b39",'important');
							// $('#lot_out_td_'+value.location).css("color","white",'important');
						// }else{
							// $('#lot_out_td_'+value.location).css("background-color","white",'important');
							// $('#lot_out_td_'+value.location).css("color","black",'important');
						// }


						// $('#lot_out_td_WI').css("background-color","white",'important');
						// $('#lot_out_td_WI').css("color","black",'important');
						// $('#lot_out_td_WI').css("font-weight","bold",'important');

						// $('#lot_out_td_EI').css("background-color","white",'important');
						// $('#lot_out_td_EI').css("color","black",'important');
						// $('#lot_out_td_EI').css("font-weight","bold",'important');

						$('#lot_out_td_TRUE').css("background-color","white",'important');
						$('#lot_out_td_TRUE').css("color","black",'important');
						$('#lot_out_td_TRUE').css("font-weight","bold",'important');

						$('#lot_out_td_KBI').css("background-color","white",'important');
						$('#lot_out_td_KBI').css("color","black",'important');
						$('#lot_out_td_KBI').css("font-weight","bold",'important');

						$('#lot_out_td_ARISA').css("background-color","white",'important');
						$('#lot_out_td_ARISA').css("color","black",'important');
						$('#lot_out_td_ARISA').css("font-weight","bold",'important');
					// });

					$.each(result.lot_count, function(key,value){
						$('#lot_ok_'+value.location).html(value.lot_ok);
						$('#lot_out_'+value.location).html(value.lot_out);

						$('#lot_ok_'+value.location).html(value.lot_ok);
						$('#lot_out_'+value.location).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.location).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.location).css("color","white",'important');
							$('#lot_ok_td_'+value.location).css("font-weight","bold",'important');
						}else{
							$('#lot_ok_td_'+value.location).css("background-color","white",'important');
							$('#lot_ok_td_'+value.location).css("color","black",'important');
							$('#lot_ok_td_'+value.location).css("font-weight","bold",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.location).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.location).css("color","white",'important');
							$('#lot_out_td_'+value.location).css("font-weight","bold",'important');
						}else{
							$('#lot_out_td_'+value.location).css("background-color","white",'important');
							$('#lot_out_td_'+value.location).css("color","black",'important');
							$('#lot_out_td_'+value.location).css("font-weight","bold",'important');
						}
					});

					$('#table_lot').DataTable().clear();
					$('#table_lot').DataTable().destroy();
					$('#body_table_lot').html("");
					var body_lot = "";

					$.each(result.lot_detail, function(key2,value2){
						if (value2.status_lot == 'Lot Out') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot += '<tr>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+' - '+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						if (value2.ng_name != null) {
							body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.ng_name+'</td>';
						}else{
							body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;"></td>';
						}
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.repair+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.return+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.ng_ratio.toFixed(2)+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.vendor_shortname+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.status_lot+'</td>';

						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.date_lot+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.invoice+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.employee_id+'<br>'+value2.name.replace(/(.{14})..+/, "$1&hellip;")+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.material_number+'<br>'+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.inspection_level+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.vendor_shortname+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_rec+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.qty_check+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.total_ng+'</td>';
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:right;">'+value2.ng_ratio.toFixed(2)+'</td>';
						// if (value2.ng_name != null) {
						// 	body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.ng_name+'</td>';
						// }else{
						// 	body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;"></td>';
						// }
						// body_lot += '<td style="background-color:'+color+';font-size: 1.1vw; padding-top: 2px; padding-bottom: 2px;text-align:left;">'+value2.status_lot+'</td>';
						body_lot += '</tr>';
					});

					$('#body_table_lot').append(body_lot);

					var table = $('#table_lot').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 20, 50, 100, -1 ],
						[ '20 rows', '50 rows', '100 rows', 'Show all' ]
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
						'pageLength': 20,
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

					$('#periode').html('Periode '+result.monthTitle+' ('+result.timeTitle+')');
					$('#loading').hide();
				}else{
					$('#loading').hide();
				}
			}else{
				$('#loading').hide();
			}
		});
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection