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
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
		margin:0;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	#container {
		height: 400px; 
	}

	.highcharts-figure, .highcharts-data-table table {
		min-width: 310px; 
		max-width: 800px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #EBEBEB;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}
	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}
	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}
	.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
		padding: 0.5em;
	}
	.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}
	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}
</style>
@stop

@section('header')
<section class="content-header">
	<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%; color: white; padding-bottom" onclick="InputMp();"><i class="fa fa-list"></i> Kebutuhan MP</button>
	<h1>
		Kebutuhan Manpower <span class="text-purple">完成品在庫</span>
		<small>Base On Data Accounting Dept</small>
	</h1>
</section>
@stop
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	<p style="position: absolute; color: White; top: 45%; left: 27%;">
		<span style="font-size: 40px">Loading, please wait a moment . . . <i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border" id="boxTitle">
				</div>
				<div class="box-body">
					<div id="container" style="width:100%; height:450px;"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-2" style="padding-bottom: 20px;">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="month" placeholder="Pilih Bulan" onchange="Update()">
			</div>
		</div> 
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<table id="tableStock" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width : 15%">Bulan</th>
								<th style="width : 20%">Department</th>
								<th style="width : 20%">Section</th>
								<th style="width : 20%">Jumlah</th>
							</tr>
						</thead>
						<tbody id="tableStockBody">
						</tbody>
						<tfoot >
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
	</div>
</section>

<div class="modal fade" id="modalStock">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
				<div class="modal-body table-responsive no-padding">
					<table class="table table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<th>Material</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>m&sup3;</th>
						</thead>
						<tbody id="tableBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th id="totalQty"></th>
							<th id="totalM3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <form id ="importForm" name="importForm" method="post" action="{{ url('insert/section') }}" >
	<input type="hidden" value="{{csrf_token()}}" name="_token" /> -->
	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="nav-tabs-custom tab-danger" align="center">
						<ul class="nav nav-tabs">
							<center><h3 style="background-color: rgba(126,86,134,.7); font-weight: bold; padding: 3px; margin-top: 0; color: black;">Kebutuhan MP</h3>
							</center>
						</ul>
					</div>
						<!-- <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
							Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
							Sample: <a href="{{ url('uploads/receive/sample/receive_sample.xlsx') }}">receive_sample.xlsx</a>
						</div> -->
						<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label">Bulan<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="bulan" name="bulan" placeholder="Pilih Bulan">
									</div>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
									</div>
								</div>
								<div class="form-group row">
									<center>
										<a href="{{ url('uploads/kebutuhan_mp/TemplateKebutuhanMp.xlsx') }}">TemplateKebutuhanMp.xlsx</a>
									</center>
									</div>
								<div class="modal-footer">
									<center>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button onclick="UploadMp()" class="btn btn-success">Upload</button>
									</center>
								</div>
							</div>
						</div>

						<!-- <div class="modal-body">
							<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
							Upload Excel file here:<span class="text-red">*</span>
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button onclick="UploadMp()" class="btn btn-success">Upload</button>
						</div> -->
					<!-- <div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-xs-6" style="padding-bottom: 20px;">
									<span>Bulan</span>
									<div class="input-group date">
										<div class="input-group-addon bg-green" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="bulan" name="bulan" placeholder="Pilih Bulan">
									</div>
								</div> 
								<div class="col-md-6" style="margin-bottom : 5px">
									<span>File Excel</span>
									<input type="file" class="form-control" name="file" id="file">                      
								</div> -->
								<!-- <div class="col-md-12">
									<button class="btn btn-success">Excel</button>
								</div> -->
								<!-- <div class="col-md-12" style="margin-bottom : 5px">
									<span>Department</span>
									<input type="text" class="form-control" name="department" id="department" value="{{$emp->department}}" readonly>                      
								</div>
								<div class="col-md-12" style="margin-bottom : 5px">
									<span>Section</span>
									<select class="form-control select6" id="section" name="section" data-placeholder='Pilih Section' style="width: 100%">
										<option value="">&nbsp;</option>
										@foreach($section as $row)
										<option value="{{$row->section}}">{{$row->section}}</option>
										@endforeach
									</select>                      
								</div>
								<div class="col-md-12" style="margin-bottom : 5px">
									<span>Jumlah</span>
									<input type="text" class="form-control numpad" name="jumlah" id="jumlah" value="0">                      
								</div>
								<div class="col-md-12" style="margin-bottom : 5px">
									<span>Jumlah</span>
									<select class="form-control select7" id="remark" name="remark" data-placeholder='Remark' required style="width: 100%">
                      <option value="">&nbsp;</option>
                      <option value="DIRECT">DIRECT</option>
                      <option value="INDIRECT">INDIRECT</option>
                      <option value="STAFF">STAFF</option>
                    </select>                      
								</div> -->
								<!-- <div class="col-md-12">
									<br>
									<button class="btn btn-success pull-right" onclick="CreateInsertMP()">Confirm</button>
								</div>
							</div>
						</div>
					</div> -->
				</div>
			</div>
		</div>
	</div>
<!-- </form> -->
@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillChart();
		$('.select6').select2();
		$('.select7').select2();

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	$('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm-01",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
   });

	function InputMp(){
    $('#modalCreate').modal('show');
  }

  function Reset(){
  	$('#bulan').val('');
  	$('#jumlah').val(0);
  	$('#section').val('').trigger('change');
  	$('#remark').val('').trigger('change');
  }

  function CreateInsertMP() {
  	$('#loading').show();
  	var bulan = $('#bulan').val();
  	// var department = $('#department').val();
  	// var section = $('#section').val();
  	// var jumlah = $('#jumlah').val();
  	// var remark = $('#remark').val();
  	var file = $('#file').val();

  	// if (section == '' || jumlah == '' || remark == '') {
  	// 	audio_error.play();
  	// 	$('#loading').hide();
  	// 	openErrorGritter('Error!','Masukkan Section dan Jumlah Kebutuhan MP.');
  	// }

  	var data = {
  		bulan:bulan,
  		// department:department,
  		// section:section,
  		// jumlah:jumlah,
  		// remark:remark
  		file:file
  	}

  	$.post('{{ url("input/kebutuhan_mp") }}', data, function(result, status, xhr){
  		if(result.status){
  			Reset();
  			$('#loading').hide();
  			openSuccessGritter('Success','Sukses.');
  			$("#modalCreate").modal('hide');
  			fillChart();
  		} else {
  			$('#loading').hide();
  			audio_error.play();
  			openErrorGritter('Error!',result.message);
  		}
  	})
  }

  function UploadMp(){
		$('#loading').show();
		if($('#bulan').val() == ""){
			openErrorGritter('Error!', 'Please input period');
			audio_error.play();
			$('#loading').hide();
			return false;	
		}

		var formData = new FormData();
		var newAttachment  = $('#upload_file').prop('files')[0];
		var file = $('#upload_file').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('bulan', $("#bulan").val());

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/kebutuhan/mp') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
					$('#bulan').val("");
					$('#upload_file').val("");
					$('#modalCreate').modal('hide');
					$('#loading').hide();
					fillChart();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

  function Update(){
  	var month = $('#month').val();

  	var data ={
  		month:month
  	}

  	$.get('{{ url("#") }}', data, function(result, status, xhr){
 
  	})
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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillChart(){
		$.get('{{ url("fetch/kebutuhan/manpower") }}', function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					$.each(result.jsonData, function(key, value) {
						var forecasts = value.forecasts;
						var produksi = value.produksi;
						var rekrut = value.rekrut;
						var aktual = value.aktual;
						var month = value.month;

						var mp_aktual = mp_aktual;
						var request_produksi = request_produksi;
						var diterima_hr = diterima_hr;
						var hr_recruitment = hr_recruitment;
					});

					var a = result.jml_recruit;
					var b = result.month;

					$('#tableStock').DataTable().clear();
					$('#tableStock').DataTable().destroy();
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#boxTitle').html('<i class="fa fa-info-circle"></i><h4 class="box-title">Kebutuhan MP Produksi Bulan '+b+' : <b>'+a[0].jumlah_recruit+' Orang</b></h4>');


					$('#tableStockBody').html("");
					
					var tableStockData = '';

					$.each(result.table, function(key, value) {
						tableStockData += '<tr>';
						tableStockData += '<td>'+ value.month +'</td>';
						tableStockData += '<td>'+ value.department +'</td>';
						tableStockData += '<td>'+ value.section +'</td>';
						tableStockData += '<td>'+ value.count +'</td>';
						tableStockData += '</tr>';
					});

					$('#tableStockBody').append(tableStockData);
					$('#tableStock tfoot th').each( function () {
						var title = $(this).text();
						$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
					});

					var table = $('#tableStock').DataTable({
						'dom': 'Bfrtip',
						'responsive': true,
						"pageLength": 25,
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
					$('#tableStock tfoot tr').appendTo('#tableStock thead');
					
					var data = result.jsonData;

					var xAxis = []
					, month = []
					, forecasts = []
					, rekrut = []
					, aktual = []
					, produksi = []

					, mp_aktual = []
					, request_produksi = []
					, diterima_hr = []
					, hr_recruitment = []

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].forecasts);
						month.push(data[i].month);
						forecasts.push(parseInt(data[i].forecasts));
						rekrut.push(parseInt(data[i].rekrut));
						produksi.push(parseInt(data[i].produksi));
						aktual.push(parseInt(data[i].aktual));

						mp_aktual.push(parseInt(data[i].mp_aktual));
						request_produksi.push(parseInt(data[i].request_produksi));
						diterima_hr.push(parseInt(data[i].diterima_hr));
						hr_recruitment.push(parseInt(data[i].hr_recruitment));
					}

					var chart;
					chart = new Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Grafik Rencana Man Power dan Rencana Recruit'
						},
						xAxis: {
							categories: month,
							gridLineWidth: 1,
							scrollbar: {
								enabled: true
							}
						},
						yAxis: {
							min: 1,
							title: {
								text: 'Kebutuhan Manpower'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
								}
							},
							type: 'logarithmic'
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											viewModalDetail(this.category,this.series.name,result.dateto);
										}
									}
								},
								borderWidth: 0,
								dataLabels: {
									enabled: true,
									format: '{point.y}'
								}
							},
							column: {
								color:  Highcharts.ColorString,
								borderRadius: 1,
								dataLabels: {
									enabled: true
								}
							}
						},
						series: [{
							name: 'MP Forecast',
							data: forecasts,
						}, {
							name: 'MP Aktual',
							data: mp_aktual,
						}, {
							name: 'Prod Request',
							data: request_produksi,
						}, {
							name: 'HR Recruitment',
							data: hr_recruitment,
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
}

function modalStock(destination, location){
	if(location == 'Production'){
		var status = ['0', 'M'];
	}
	if(location == 'InTransit'){
		var status = ['1'];
	}
	if(location == 'FSTK'){
		var status = ['2'];
	}
	var data = {
		status:status,
		destination:destination
	}

	$.get('{{ url("fetch/tb_stock") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#tableBody').html("");
				$('.modal-title').html("");
				$('.modal-title').html('Location <b>' + result.location+ '</b> for Destination <b>' +result.title+'</b>');
				var tableData = '';
				var totalQty = 0;
				var totalM3 = 0;
				$.each(result.table, function(key, value) {
					totalQty += value.actual;
					totalM3 += (((value.length*value.width*value.height)/value.lot_carton)*value.actual);
					tableData += '<tr>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.actual +'</td>';
					tableData += '<td>'+ (((value.length*value.width*value.height)/value.lot_carton)*value.actual).toFixed(2).toLocaleString() +'</td>';
					tableData += '</tr>';
				});
				$('#tableBody').append(tableData);
				$('#modalStock').modal('show');
				$('#totalQty').html('');
				$('#totalQty').append(totalQty.toLocaleString());
				$('#totalM3').html('');
				$('#totalM3').append(totalM3.toFixed(2).toLocaleString());
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
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