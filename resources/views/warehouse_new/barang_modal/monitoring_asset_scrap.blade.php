@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
		
	</h1>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px; padding-left: 0px;">
			<div class="col-xs-2">
				<div class="input-group date pull-right" style="text-align: center;">
					<div class="input-group-addon bg-green">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control monthpicker" name="period" id="period" placeholder="Select Period">	
				</div>
			</div>

			<div class="col-xs-2" style="padding: 0px;">
				<button id="search" onclick="fillTable()" class="btn btn-primary">Search</button>
			</div>

			<div class="col-xs-4 pull-right" style="padding-right: 0px;">
				<!-- <div class="col-xs-4 pull-right" style="padding: 0px;">
					<a href="{{ url("/index/shipping_agency") }}" class="btn btn-info" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Shipping Line</a>
				</div>
				<div class="col-xs-4 pull-right" style="margin-right: 10px; padding: 0px;">
					<a href="{{ url("/index/shipping_order") }}" class="btn btn-info" style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Booking List</a>
				</div> -->
			</div>
		</div>

		<div class="col-xs-9" style="padding-top: 0px; padding-bottom: 15px;">
			<div class="col-xs-12" style="padding: 0px;">
				<div id="container1" style="height: 525px;"></div>				
			</div>
		</div>

		<div class="col-xs-3" style="padding-bottom: 0px;">
			<div class="box box-solid" style="margin-bottom: 0px;">
				<div class="box-body">
					<table id="tableResume" class="table table-bordered" style="width: 100%; font-size: 14px; border: 3px solid black;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 10%; font-size: 18px; font-weight: bold;">Category</th>
								<th style="width: 7%; font-size: 18px; font-weight: bold;">Total</th>
							</tr>
						</thead>
						<tbody id="tableBodyResume">
							<tr style="height: 60px">
								<td id="teus_subject">Disposal<br>&nbsp;</td>
								<td id="teus_plan" style="font-weight: bold; font-size: 20px;"></td>
							</tr>
							<tr style="height: 60px">
								<td id="or_subject">Dokumen</td>
								<td id="or_plan" style="font-weight: bold; font-size: 20px;"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table id="tableList" class="table table-bordered" style="width: 100%; font-size: 12px;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%">Tanggal</th>
								<th style="width: 1%">Kode Asset</th>
								<th style="width: 5%">Deskripsi Barang</th>
								<th style="width: 1%">Qty</th>
								<th style="width: 1%">Diajukan Oleh</th>
								<th style="width: 1%">Pengajuan</th>
								<th style="width: 1%">Umur dan Asal FA</th>
								<th style="width: 1%">PO Vendor</th>
								<th style="width: 1%">Pengajuan Disposal ke BC</th>
								<th style="width: 1%">Proses Scrap</th>
								<th style="width: 1%">Pengajuan Dokumen BC</th>
								<th style="width: 1%">Vendor Transfer</th>
								<th style="width: 1%">FA Keluar YMPI</th>
								<th style="width: 1%">Dokumen</th>
							</tr>
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
	
</div>

<div class="modal fade" id="modalCategory">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><span style="font-size: 20px;font-weight: bold;">Evidence <span id="category_evidence"></span> </span></center>
			</div>
			<div class="modal-body">
				<span>Upload File Evidence</span>
				<input type="file" class="form-control" id="upload_file" name="upload_file">
			</div>
			<div class="modal-footer">
          <input type="hidden" id="id_asset">
				 <button type="button" onclick="update_evidence()" class="btn btn-success"><i class="fa fa-pencil"></i> Submit Evidence</button>
			</div>
		</div>
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
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});
		fillTable();
		setInterval(fillTable, 10*60*1000);


	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		var period = $('#period').val();

		var data = {
			period : period,
		}

		$.get('{{ url("fetch/monitoring/fixed_asset/disposal/scrap") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableBodyList').html("");

				var tableData = "";
				for (var i = 0; i < result.data.length; i++) {

					tableData += '<tr>';
					tableData += '<td>'+ getFormattedDate(new Date(result.data[i].disposal_request_date)) +'</td>';
					tableData += '<td>'+ result.data[i].fixed_asset_id +'</td>';
					tableData += '<td style="text-align:left">'+ result.data[i].fixed_asset_name +'</td>';
					tableData += '<td>1</td>';
					tableData += '<td style="text-align:left">'+ result.data[i].pic_app.split('/')[1] +'</td>';
					if (result.data[i].status == "created") {
						tableData += '<td style="background-color:red;color:white;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(PIC Asset)</td>';
					} 
					else if (result.data[i].status == "pic") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Staff Accounting)</td>';
					}
					else if (result.data[i].status == "fa_control") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager)</td>';
					}
					else if (result.data[i].status == "manager") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager '+result.data[i].pic_incharge+')</td>';
					}
					else if (result.data[i].status == "manager_disposal") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(General Manager)</td>';
					}
					else if (result.data[i].status == "dgm") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Approval General Manager)</td>';
					}
					else if (result.data[i].status == "gm") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Manager Accounting)</td>';
					}
					else if (result.data[i].status == "acc_manager") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(Director Finance)</td>';
					}
					else if (result.data[i].status == "director_fin") {
						tableData += '<td style="background-color:orange;color:black;vertical-align:middle">Approval <br> <span style="font-weight:bold;font-size:10px">(President Director)</td>';
					}
					else if (result.data[i].status == "presdir") {
						tableData += '<td style="background-color:green;color:black;vertical-align:middle">Received By <br> <span style="font-weight:bold;font-size:10px">(Logistic)</td>';
					}
					else if (result.data[i].status == "new_pic") {
						tableData += '<td style="background-color:green;color:black;vertical-align:middle">Completed</td>';
					}
					else{
						tableData += '<td style="background-color:green;color:white;vertical-align:middle">Complete</td>';
					}

					if (result.data[i].status == "new_pic") {
						var param = [
						'umur_fa',
						'po_vendor',
						'disposal',
						'scrap',
						'dokumen_bc',
						'transfer',
						'keluar',
						'dokumen_final',
						];
						tableData += '<td onclick="upload_evidence(\''+param[0]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[1]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[2]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[3]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[4]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[5]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[6]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
						tableData += '<td onclick="upload_evidence(\''+param[7]+'\',\''+result.data[i].id+'\')" style="cursor:pointer"></td>';
					}else{
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
					}
					tableData += '</tr>';
				}
				
				$('#tableBodyList').append(tableData);
				
				var total_disposal = 0;
				var total_all = 0;

				var date = [];
				var disposal = [];
				var all = [];

				// var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

				for (var i = 0; i < result.data_chart.length; i++) {
					// var d = new Date(result.data_chart[i].bulan)
					// date.push(d.getDate()+'-'+monthNames[d.getMonth()]);
					date.push(result.data_chart[i].bulan);

					disposal.push(parseInt(result.data_chart[i].jumlah));
					all.push(parseInt(result.data_chart[i].jumlah));				

					total_disposal += parseInt(result.data_chart[i].jumlah);
					total_all += parseInt(result.data_chart[i].jumlah);				

				}

				var css = 'style="font-weight: normal; font-style: italic;"';

				var persen_disposal = Math.round(total_all/total_disposal*100);

				// $('#teus_etd').html(total_disposal + ' <small '+ css +'>('+ persen_disposal +'%)</small>');
				$('#total_disposal').html(total_disposal + ' <small '+ css +'>('+ persen_disposal +'%)</small>');

				Highcharts.chart('container1', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Monitoring Fixed Asset Disposal Scrap ('+result.month+')</span>'
					},
					xAxis: {
						categories: date
					},
					yAxis: {
						enabled: true,
						title: {
							enabled: true,
							text: "Jumlah"
						},
						tickInterval: 1
					},
					legend: {
						enabled: true
					},
					exporting: {
						enabled: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					credits: {
						enabled: false
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'top',
						x: 1,
						y: 0,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
						shadow: true
					},
					plotOptions: {
						column: {
							// stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							borderColor: '#212121',
							dataLabels: {
								enabled: true,
								style:{
									textOutline: false
								},
								formatter: function() {
									if (this.y != 0) {
										return this.y;
									} else {
										return null;
									}
								}
							},
						}, 
						series: {
							cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(event.point.category);

									}
								}
							},
						},
					},
					series: [
					{
						name: 'Disposal',
						data: disposal,
						color: '#42f5b0'
					}
					// ,{
					// 	name: 'All',
					// 	data: all,
					// 	color: '#d50000'
					// }
					]
				});
			}
		});
}

// function showDetail(category) {
// 	var period = $('#period').val();
// 	var date = category;

// 	var data = {
// 		period : period,
// 		date : date
// 	}

// 	$.get('{{ url("fetch/resume_shipping_order_detail") }}', data, function(result, status, xhr){
// 		if(result.status){
// 			$('#tableDetailBody').html("");
// 			$('#tableDetailRefBody').html("");

// 			$('#title_modal').text('Shipping Booking Management Booking Details on ' + result.st_date);

// 			var detail = '';
// 			var concat = '';
// 			$.each(result.detail, function(key, value){
// 				var color = '';
// 				var check = 'BOOKING CONFIRMED';
// 				var status = value.status;

// 				if(status.includes(check)){
// 					concat += value.ycj_ref_number;
// 					color = 'style="background-color: rgb(204, 255, 255);"';
// 				}

// 				detail += '<tr>';
// 				detail += '<td '+color+'>'+value.ycj_ref_number+'</td>';
// 				detail += '<td '+color+'>'+value.shipper+'</td>';
// 				detail += '<td '+color+'>'+value.port_loading+'</td>';
// 				detail += '<td '+color+'>'+value.port_of_delivery+'</td>';
// 				detail += '<td '+color+'>'+value.country+'</td>';
// 				detail += '<td '+color+'>'+value.plan_teus+'</td>';
// 				detail += '<td '+color+'>'+value.plan+'</td>';
// 				detail += '<td '+color+'>'+(value.fortyhc || '' )+'</td>';
// 				detail += '<td '+color+'>'+(value.forty || '' )+'</td>';
// 				detail += '<td '+color+'>'+(value.twenty || '' )+'</td>';
// 				detail += '<td '+color+'>'+(value.booking_number || '')+'</td>';
// 				detail += '<td '+color+'>'+value.carier+'</td>';
// 				detail += '<td '+color+'>'+value.nomination+'</td>';
// 				detail += '<td '+color+'>'+value.application_rate+'</td>';
// 				detail += '<td '+color+'>'+value.status+'</td>';
// 				detail += '</tr>';
// 			});
// 			$('#tableDetailRefBody').append(detail);


// 			var detail = '';
// 			$.each(result.resume, function(key, value){
// 				var color = '';
// 				if(concat.includes(value.ycj_ref_number)){
// 					color = 'style="background-color: rgb(204, 255, 255);"';
// 				}else{
// 					color = 'style="background-color: rgb(255, 204, 255);"';
// 				}

// 				detail += '<tr>';
// 				detail += '<td '+color+'>'+value.ycj_ref_number+'</td>';
// 				detail += '<td '+color+'>'+value.shipper+'</td>';
// 				detail += '<td '+color+'>'+value.port_loading+'</td>';
// 				detail += '<td '+color+'>'+value.port_of_delivery+'</td>';
// 				detail += '<td '+color+'>'+value.country+'</td>';
// 				detail += '<td '+color+'>'+value.plan_teus+'</td>';
// 				detail += '<td '+color+'>'+value.plan+'</td>';
// 				detail += '<td '+color+'>'+(value.invoice || '-')+'</td>';
// 				detail += '<td '+color+'>'+(value.actual_departed || '-')+'</td>';
// 				detail += '</tr>';
// 			});
// 			$('#tableDetailBody').append(detail);


// 			$('#modalDetail').modal('show');
// 		}
// 		else{
// 			openErrorGritter('Error!', result.message);
// 		}
// 	});
// }


	function upload_evidence(cat,id){
    $("#id_asset").val(id);
		$('#modalCategory').modal('show');
		$('#category_evidence').html(cat);
	}


  function update_evidence() {
    if ($("#upload_file").val() == "") {
      openErrorGritter("Error","Bukti Penanganan Harus Diisi");
      return false;
    }

    var formData = new FormData();
    formData.append('id', $("#id_asset").val());
    formData.append('upload_file', $('#upload_file').prop('files')[0]);

    $.ajax({
      url:"{{ url('post/monitoring/fixed_asset/disposal/scrap') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        openSuccessGritter("Success","Evidence Berhasil Di Upload");
        $('#modalCategory').modal("hide");
        fillTable();
      },
      error: function (response) {
        openErrorGritter("Error",result.datas);
        $('#modalPenanganan').modal("hide");
      },
    });

  }


function getFormattedDate(date) {
  var year = date.getFullYear();

  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

  var month = date.getMonth();

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  
  return day + '-' + monthNames[month] + '-' + year;
}

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


</script>
@endsection