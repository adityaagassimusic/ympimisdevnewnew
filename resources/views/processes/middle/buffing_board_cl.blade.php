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

	.akan {
		-webkit-animation: akan 1s infinite; 
		-moz-animation: akan 1s infinite;
		-o-animation: akan 1s infinite;
		animation: akan 1s infinite;
	}
	
	@-webkit-keyframes akan {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: rgb(243, 156, 18);
		}
	}

	.selesai {
		-webkit-animation: selesai 1s infinite;
		-moz-animation: selesai 1s infinite;
		-o-animation: selesai 1s infinite;
		animation: selesai 1s infinite;
	}

	@-webkit-keyframes selesai {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #f73939;
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
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 18px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0; background-color:#4ff05a;">Sedang</th>
						<th style="width: 0.66%; padding: 0; background-color:#ffd03a">Akan</th>
						<th style="width: 0.66%; padding: 0;">#1</th>
						<th style="width: 0.66%; padding: 0;">#2</th>
						<th style="width: 0.66%; padding: 0;">#3</th>
						<th style="width: 0.66%; padding: 0;">#4</th>
						<th style="width: 0.66%; padding: 0;">#5</th>
						<th style="width: 0.66%; padding: 0;">#6</th>
						<th style="width: 0.66%; padding: 0;">#7</th>
						<th style="width: 0.66%; padding: 0;">#8</th>
						<th style="width: 0.66%; padding: 0;">#9</th>
						<th style="width: 0.66%; padding: 0;">#10</th>
						<th style="width: 0.66%; padding: 0;">Jumlah</th>
						<th style="width: 0.66%; padding: 0; background-color: #f76a6a">Selesai</th>
					</tr>
				</thead>
				<tbody id="buffingTableBody" style="font-size: 18px;">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="detail_modal">
	<div class="modal-dialog" style="width: 60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
				</button>
				<h4 class="modal-title" id="queue-title"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body">
							<table id="detailTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="">#</th>
										<th style="">Material</th>
										<th style="">Description</th>
										<th style="">Model</th>
										<th style="">Key</th>
										<th style="">Created At</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
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

	jQuery(document).ready(function() {
		fetchTable();
		setTimeSelesai();
		setTimeSedang();

		setInterval(fetchTable, 3000);
		setInterval(setTimeSelesai, 1000);
		setInterval(setTimeSedang, 1000);

	});

	var selesai_time = [];
	var sedang_time = [];
	var akan_bff = [];
	var totalAkan = [0,0,0,0,0,0,0];


	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	function setTimeSelesai() {
		for (var index = 0; index <= selesai_time.length; index++) {
			if(selesai_time[index]){
				$('#selesai_hour_' + index).html(pad(parseInt(diff_seconds(new Date(), selesai_time[index]) / 3600)));
				$('#selesai_minute_' + index).html(pad(parseInt((diff_seconds(new Date(), selesai_time[index]) % 3600) / 60)));
				$('#selesai_second_' + index).html(pad(diff_seconds(new Date(), selesai_time[index]) % 60));
			}else{
				$('#selesai_hour_' + index).html('&nbsp;');
				$('#selesai_minute_' + index).html('&nbsp;');
				$('#selesai_second_' + index).html('&nbsp;');
			}
		}
	}

	function setTimeSedang() {
		for (var index = 0; index <= sedang_time.length; index++) {
			if(sedang_time[index]){
				$('#sedang_hour_' + index).html(pad(parseInt(diff_seconds(new Date(), sedang_time[index]) / 3600)));
				$('#sedang_minute_' + index).html(pad(parseInt((diff_seconds(new Date(), sedang_time[index]) % 3600) / 60)));
				$('#sedang_second_' + index).html(pad(diff_seconds(new Date(), sedang_time[index]) % 60));
			}else{
				$('#sedang_hour_' + index).html('&nbsp;');
				$('#sedang_minute_' + index).html('&nbsp;');
				$('#sedang_second_' + index).html('&nbsp;');
			}
		}
	}

	function setTimeAkan(index) {
		if(!akan_bff[index]){
			totalAkan[index]++;
			return pad(parseInt(totalAkan[index] / 3600)) + ':' + pad(parseInt((totalAkan[index] % 3600) / 60)) + ':' + pad((totalAkan[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function showQueue(id) {

		$('#detailTable').DataTable().destroy();
		$('#queue-title').text(id.toUpperCase() + ' QUEUES');

		var data = {
			rack:id
		}

		var table = $('#detailTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default'
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/middle/buffing_board_cl_detail") }}",
				"data" : data
			},
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			"order": [[ 0, 'asc' ]],
			"columns": [
			{ "data": "material_num" },
			{ "data": "material_num" },
			{ "data": "material_description" },
			{ "data": "model" },
			{ "data": "key" },
			{ "data": "created_at" }
			]
		});

		table.on( 'order.dt search.dt', function () {
			table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				cell.innerHTML = i+1;
			} );
		} ).draw();

		$('#detail_modal').modal('show');

	}

	function fetchTable(){

		$.get('{{ url("fetch/middle/buffing_board_cl") }}', function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					selesai_time = [];
					sedang_time = [];

					akan_bff = [];

					$('#buffingTableBody').html("");
					var buffingTableBody = "";
					var i = 1;
					var color2 = "";
					var colorSelesai = "";

					$.each(result.boards, function(index, value){
						if (index % 2 === 0 ) {
							if (value.employee_id) {
								color = '';

								if (value.dev_akan_detected == 0)
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
								else
									colorSelesai = '';
							} else {
								color = '';
								color2 = '';
								colorSelesai = '';
							}
						} else {
							if (value.employee_id) {
								color = 'style="background-color: #575c57"';

								if (value.dev_akan_detected == 0)
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
								else
									colorSelesai = '';
							} else {
								color = 'style="background-color: #575c57"';
								color2 = '';
								colorSelesai = '';
							}
						}

						if (value.dev_akan_detected == 0) {
							akan_time = value.akan_time;
							akan = "";
							akan_bff.push(false);
						} else {
							akan = value.akan;
							akan_time = "";
							akan_bff.push(true);
							totalAkan[index] = 0;
						}

						if (value.dev_sedang_detected == 1) {
							sedang_time.push(new Date(value.sedang_time));
						} else {
							sedang_time.push(false);
						}

						if (value.dev_selesai_detected == 1) {
							selesai_time.push(new Date(value.selesai_time));
						} else {
							selesai_time.push(false);
						}

						buffingTableBody += '<tr '+color+'>';
						buffingTableBody += '<td height="5%" style="font-size:2vw;">'+value.ws+'</td>';
						buffingTableBody += '<td>'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';

						if(value.dev_sedang_detected == 1){
							buffingTableBody += '<td style="color:#a4fa98">'+value.sedang+'<br>';
							buffingTableBody += '<span id="sedang_hour_'+index+'">'+pad(parseInt(diff_seconds(new Date(), sedang_time[index]) / 3600))+'</span>:';
							buffingTableBody += '<span id="sedang_minute_'+index+'">'+pad(parseInt((diff_seconds(new Date(), sedang_time[index]) % 3600) / 60))+'</span>:';
							buffingTableBody += '<span id="sedang_second_'+index+'">'+pad(diff_seconds(new Date(), sedang_time[index]) % 60)+'</span>';
							buffingTableBody += '</td>';
						}else{
							buffingTableBody += '<td></td>';
						}

						buffingTableBody += '<td '+color2+'>'+akan+'<p>'+setTimeAkan(index)+'</p></td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_1+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_2+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_3+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_4+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_5+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_6+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_7+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_8+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_9+'</td>';
						buffingTableBody += '<td style="color:#F2F2F2">'+value.queue_10+'</td>';
						buffingTableBody += '<td id="clkey-'+i+'" onclick="showQueue(id)" style="cursor:pointer; color:#fff; font-size:2vw;">'+value.jumlah+'</td>';


						if(value.dev_selesai_detected == 1){
							buffingTableBody += '<td '+colorSelesai+'>'+value.selesai+'<br>';
							buffingTableBody += '<span id="selesai_hour_'+index+'">'+pad(parseInt(diff_seconds(new Date(), selesai_time[index]) / 3600))+'</span>:';
							buffingTableBody += '<span id="selesai_minute_'+index+'">'+pad(parseInt((diff_seconds(new Date(), selesai_time[index]) % 3600) / 60))+'</span>:';
							buffingTableBody += '<span id="selesai_second_'+index+'">'+pad(diff_seconds(new Date(), selesai_time[index]) % 60)+'</span>';
							buffingTableBody += '</td>';
						}else{
							buffingTableBody += '<td></td>';
						}

						buffingTableBody += '</tr>';

						i += 1;

						data2 = {
							employee_id: value.employee_id
						}

					});

$('#buffingTableBody').append(buffingTableBody);

}
else{
	alert('Attempt to retrieve data failed.');
}
}
})
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