@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		border-top: 2px solid white;
		vertical-align: middle;
		text-align: center;
		padding:1px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}
	.content{
		color: white;
		font-weight: bold;
	}

	hr {
		margin: 0px;
	}

	.alarm {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: alarm 1s infinite;  /* Safari 4+ */
		-moz-animation: alarm 1s infinite;  /* Fx 5+ */
		-o-animation: alarm 1s infinite;  /* Opera 12+ */
		animation: alarm 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes alarm {
		0%, 49% {
			background: rgba(247, 109, 109, 0);
			color: white;
		}
		50%, 100% {
			background: rgba(247, 109, 109, 100);
			color: black;
		}
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px;">
	<input type="hidden" value="{{ $loc }}" id="loc">
	<!-- <span style="padding-top: 0px">
		<center><h1><b>{{ $page }}</b></h1></center>
	</span> -->
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0; background-color:#4ff05a;">Sedang</th>
						<th style="width: 0.66%; padding: 0; background-color:#f7ab6d;">Akan</th>
						<th style="width: 0.66%; padding: 0;">#1</th>
						<th style="width: 0.66%; padding: 0;">#2</th>
						<th style="width: 0.66%; padding: 0;">#3</th>
						<th style="width: 0.66%; padding: 0;">#4</th>
						<th style="width: 0.66%; padding: 0;">#5</th>
						<th style="width: 0.66%; padding: 0;">Jumlah</th>
					</tr>
				</thead>
				<tbody id="KPPTableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="modal fade" id="AntrianModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="color: black; padding-bottom: : 0px;">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
					<br><h4 class="modal-title" id="judul_table"></h4>
				</div>
				<div class="modal-body" style="padding-top: 0px;">
					<div class="row">
						<div class="col-md-12">
							<table id="tableDetail" class="table table-bordered" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">No.</th>
										<th style="width: 15%;">WS</th>
										<th style="width: 25%;">Material Number</th>
										<th style="width: 45%;">Material Description</th> 
									</tr>
								</thead>
								<tbody id="bodyTableDetail" style="color: black">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchTable();
		setInterval(fetchTable, 3000);
	});

	function fetchTable(){
		var loc = $('#loc').val();

		var data = {
			loc : loc,
			number : '{{ Request::segment(4) }}'
		}
		$.get('{{ url("fetch/kpp_board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					sedang = [];
					sedang_kosong = [];
					total_antrian = [];

					$('#KPPTableBody').empty();
					var body = "";

					$.each(result.ws, function(index, value){
						total_antrian.push(0);
					})

					$.each(result.ws, function(index, value){
						var maxs = 5;
						var max_garis = 0;
						body += '<tr>';
						body += '<td>'+value.work_station+'</td>';

						$.each(result.ops, function(index3, value3){
							if (value.work_station == value3.work_station && value3.device_type == 'Sedang') {
								//ORANG
								if (value3.name) {
									body += '<td><b style="font-size: 12px">'+value3.employee_id+'</b><br><span style="color: yellow; font-size: 14px; font-weight: bold">'+value3.name+'</span></td>';
								} else {
									body += '<td style="background-color: #f76d6d"> - </td>';
								}

								var date_akan = new Date(value3.doing_timestamp);
								var sekarang = new Date(result.now);

								//SEDANG
								if (value3.material_number) {
									body += '<td style="background-color: #1f8216; border-left: 2px solid red"><b style="color: yellow; font-size: 16px">'+value3.material_number+'</b><br><span style="font-size: 12px; font-weight: normal">'+value3.material_description+'</span></td>';
								} else {
									body += '<td style="background-color: #f76d6d; border-left: 2px solid red" class="alarm">'+calculate_time(date_akan, sekarang)+'</td>';
								}
							} 
						})

						$.each(result.ops, function(index3, value3){
							if(value.work_station == value3.work_station && value3.device_type == 'Akan' && loc != 'Lathe') {

								var date_akan = new Date(value3.doing_timestamp);
								var sekarang = new Date(result.now);

								if (value3.material_number) {
									body += '<td style="background-color: #1f8216"><b style="color: yellow; font-size: 16px">'+value3.material_number+'</b><br><span style="font-size: 12px; font-weight: normal">'+value3.material_description+'</span></td>';
								} else {
									body += '<td style="background-color: #f76d6d; color: black" class="alarm"> '+calculate_time(date_akan, sekarang)+' </td>';
								}
							}
						})


						if (loc == 'Lathe') {
							maxs = 6;
							max_garis = 1;
						}

						$.each(result.antrians, function(index2, value2){
							var border = '';
							if (value.work_station == value2.work_station) {
								if (total_antrian[index] == max_garis) {
									border = 'style="border-left: 2px solid red"';
								}

								total_antrian[index] = total_antrian[index] + 1;

								if (total_antrian[index] <= maxs) {
									body += '<td '+border+'><b style="color: yellow; font-size: 16px">'+value2.material_number+'</b><br><span style="font-size: 12px; font-weight: normal">'+value2.material_description+'</span></td>';
								}

							}
						});

						if (total_antrian[index] < maxs) {
							for (var i = total_antrian[index]; i < maxs; i++) {
								var border = '';
								if (i == max_garis) {
									border = 'style="border-left: 2px solid red;"';
								}

								body += '<td '+border+'></td>';
							}
						}

						if (total_antrian[index] > maxs) {
							body += '<td style="font-weight: bold; font-size: 25px; cursor: pointer" onclick="modal_open(\''+value.work_station+'\')">'+(total_antrian[index]-maxs)+'</td>';
						} else {
							body += '<td style="font-weight: bold; font-size: 25px; cursor: pointer" onclick="modal_open(\''+value.work_station+'\')">'+total_antrian[index]+'</td>';
						}

						body += '</tr>';
					});

					$('#KPPTableBody').append(body);
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			}
		})
	}

	function modal_open(ws) {
		$("#AntrianModal").modal('show');
	}

	function calculate_time(akan, sekarang) {
		// get total seconds between the times
		var delta = Math.abs(sekarang - akan) / 1000;

		// calculate (and subtract) whole days
		var days = Math.floor(delta / 86400);
		delta -= days * 86400;

		// calculate (and subtract) whole hours
		var hours = Math.floor(delta / 3600) % 24;
		delta -= hours * 3600;

		// calculate (and subtract) whole minutes
		var minutes = Math.floor(delta / 60) % 60;
		delta -= minutes * 60;

		// what's left is seconds
		var seconds = delta % 60;  // in theory the modulus is not required
		return hours+' jam <br>'+minutes+' menit '+seconds+' detik';
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};
</script>
@endsection