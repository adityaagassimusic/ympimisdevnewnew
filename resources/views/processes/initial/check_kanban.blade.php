
@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
		overflow:hidden;
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
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control</small>
	</h1>

	<ol class="breadcrumb">
		<li>
			<div class="form-group">
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="bulan" name="bulan" onchange="fillTable()" placeholder="select month">
				</div>
			</div>
		</li>
	</ol>

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-md-8 col-md-offset-2" style="margin-bottom: 2%">
			<center>
				<button class="btn btn-default location" onclick="changeLoc(this, 'LATHE')" style="margin-right: 2vw;"><i class="fa fa-map-pin"></i> LATHE</button>
				<button class="btn btn-default location" onclick="changeLoc(this, 'MC1')" style="margin-right: 2vw"><i class="fa fa-map-pin"></i> MC1</button>
				<button class="btn btn-default location" onclick="changeLoc(this, 'MC2')" style="margin-right: 2vw"><i class="fa fa-map-pin"></i> MC2</button>
				<button class="btn btn-default location" onclick="changeLoc(this, 'PRESS')" style="margin-right: 2vw"><i class="fa fa-map-pin"></i> PRESS</button>
				<button class="btn btn-default location" onclick="changeLoc(this, 'SANDING')" style="margin-right: 2vw"><i class="fa fa-map-pin"></i> SANDING</button>
				<button class="btn btn-default location" onclick="changeLoc(this, 'ANEALING')" style="margin-right: 2vw"><i class="fa fa-map-pin"></i> ANEALING</button>
				<br>

				<button class="btn btn-success pull-left" onclick="changeCat('actual')" style="vertical-align: middle; font-weight: bold; margin-top: 10px"><i class="fa fa-download"></i> ACTUAL</button>
				<span style="font-size: 3vw; color: green;" id="kanban_stat"><i class="fa fa-angle-double-down"></i> KANBAN ACTUAL <i class="fa fa-angle-double-down"></i>
				</span>
				<button class="btn btn-danger pull-right" onclick="changeCat('stock')" style="vertical-align: middle; font-weight: bold; margin-top: 10px"><i class="fa fa-upload"></i> STOCK</button>
			</center>
			<div class="input-group col-md-12" style="text-align: center;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: black;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="hidden" id="status" value="actual">
				<input type="text" style="text-align: center; border-color: black; font-size: 3vw; height: 70px" class="form-control" id="tag" name="tag" placeholder="Scan Kanban Here" required="">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: black;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr style="border: 1px solid black">
						<th width="10%">Material Number</th>
						<th width="20%">Material Description</th>
						<th width="8%">Process</th>
						<th width="8%">Total Kanban</th>
						<th width="8%">Plan</th>
						<th width="8%">Actual</th>
						<th width="8%">Diff <br> (Actual:Plan)</th>
						<th width="8%">Diff <br> (Plan:Total_Kanban)</th>
						<!-- <th width="8%">Inactive</th> -->
						<!-- <th width="5%">Action</th> -->
					</tr>
				</thead>
				<tbody id="tableMasterBody">
				</tbody>
			</table>	
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="detailKanban">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><center>Kanban Status <br><span id="kanban"></span></center></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th>No</th>
									<th>No Kanban</th>
									<th>Status</th>
									<th>Last Update</th>
								</tr>
							</thead>
							<tbody id="bodyDetail">
							</tbody>
						</table>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<br>
						<button class="btn btn-danger pull-right" data-dismiss='modal'><i class="fa fa-close"></i> Close</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	

	var actual_data = [];
	var locs = '';
	var total_kanban = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillTable();

		$('#bulan').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});

		$("#tag").focus();

	});	


	function fillTable(){
		$("#loading").show();
		var data = {
			bulan : $("#bulan").val()
		}
		$.get('{{ url("fetch/tpro/resume_kanban") }}', data, function(result, status, xhr){
			$("#loading").hide();
			if(result.status){
				total_kanban = result.total_kanbans;

				var hasil = [];
				total_kanban.reduce(function(res, value) {
					if (!res[value.product_gmc]) {
						res[value.product_gmc] = { product_gmc: value.product_gmc, qty: 0 };
						hasil.push(res[value.product_gmc])
					}
					res[value.product_gmc].qty += value.num;
					return res;
				}, {});

				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();
				$('#tableMasterBody').empty();
				var bodyResume = "";

				actual_data = result.act_data;

				for (var i = 0; i < result.datas.length; i++) {
					bodyResume += '<tr style="cursor:pointer;" onclick="check_detail(\''+result.datas[i]['material_number']+'\')">';
					bodyResume += '<td>'+result.datas[i]['material_number']+'</td>';
					bodyResume += '<td style="text-align:left;padding-left:5px">'+result.datas[i]['material_description']+'</td>';
					bodyResume += '<td>'+(result.datas[i]['location'] || '')+'</td>';

					total_plan = Math.ceil(result.datas[i]['quantity']*2/result.datas[i]['lot']) + Math.ceil((result.datas[i]['lead_time']*result.datas[i]['quantity']/420)/result.datas[i]['lot']);

					var stat_total = false;
					var total_kbn = 0;

					$.each(hasil, function(index2, value2){
						if (value2.product_gmc == result.datas[i]['material_number']) {
							bodyResume += '<td>'+value2.qty+'</td>';
							stat_total = true;
							total_kbn = value2.qty;
						}
					})

					if (!stat_total) {
						bodyResume += '<td>0</td>';
					}

					bodyResume += '<td>'+total_plan+'</td>';
					if (result.datas[i]['actual_kanban'] == 0) {
						bodyResume += '<td onclick="openModalKanban(\''+result.datas[i]['material_number']+'\', \''+result.datas[i]['material_description']+'\',\''+result.datas[i]['location']+'\')" style="background-color: #ff8c8c">Belum Check</td>';
						// +result.datas[i]['actual_kanban']+
					} else {
						bodyResume += '<td onclick="openModalKanban(\''+result.datas[i]['material_number']+'\', \''+result.datas[i]['material_description']+'\',\''+result.datas[i]['location']+'\')">'+result.datas[i]['actual_kanban']+'</td>';
					}
					bodyResume += '<td>'+(parseInt(result.datas[i]['actual_kanban']) - total_plan)+'</td>';
					bodyResume += '<td>'+(total_plan - total_kbn)+'</td>';
					bodyResume += '</tr>';
				}

				$('#tableMasterBody').append(bodyResume);

				// $('#tableMaster tfoot th').each( function () {
				// 	var title = $(this).text();
				// 	$(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'"/>' );
				// } );
				
				var table = $('#tableMaster').DataTable({
					'dom': 'Bfrtip',
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
					'pageLength': 25,
					'searching'   	: true,
					'ordering'		: true,
					'order': [[5, 'desc']],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
				});

				// table.columns().every( function () {
				// 	var that = this;

				// 	$( 'input', this.footer() ).on( 'keyup change', function () {
				// 		if ( that.search() !== this.value ) {
				// 			that
				// 			.search( this.value )
				// 			.draw();
				// 		}
				// 	} );
				// } );

				// $('#tableMaster tfoot tr').appendTo('#tableMaster thead');  

			}
			else{
				openErrorGritter('Error!', 'Loading Failed.');
				audio_error.play();
			}
		});
}

function check_detail(material_number) {
	console.log(material_number);
}

$("#tag").on("keydown", function(event) {
	if(event.which == 13 || event.which == 9) {
		if (locs == '') {
			openErrorGritter('Gagal', 'Mohon Pilih Lokasi Terlebih Dahulu');
			$(this).val('');
			return false;
		}

		var data = {
			tag_number : $(this).val(),
			period : $("#bulan").val(),
			status : $("#status").val(),
			location : locs
		}

		$("#loading").show();

		$.get('{{ url("fetch/tpro/scan_kanban") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#tag").val('');
				$("#tag").focus();
				openSuccessGritter('Sukses', 'Update Data Berhasil');
				fillTable();
			} else {
				openErrorGritter('Gagal', result.message);
				$("#loading").hide();
				$(this).val('');
			}
		})
	}
});

function changeCat(category) {
	$("#status").val(category);

	if (category == 'actual') {
		$("#kanban_stat").css('color', 'green');
		$("#kanban_stat").html('<i class="fa fa-angle-double-down"></i> KANBAN ACTUAL <i class="fa fa-angle-double-down"></i>');
	} else {
		$("#kanban_stat").css('color', 'red');
		$("#kanban_stat").html('<i class="fa fa-angle-double-down"></i> KANBAN STOCK <i class="fa fa-angle-double-down"></i>');
	}

	$("#tag").focus();
}

function changeLoc(elem, loc) {
	locs = loc;

	$('.location').each(function(i, obj) {
		$(obj).css('color', '#444');
		$(obj).css('border-color', '#ddd');
	});	

	$(elem).css('color', 'red');
	$(elem).css('border-color', 'red');

	fillTable2(locs);

	$("#tag").focus();
}

function fillTable2(lokasi){
	$("#loading").show();
	var data = {
		bulan : $("#bulan").val()
	}
	$.get('{{ url("fetch/tpro/resume_kanban") }}', data, function(result, status, xhr){
		$("#loading").hide();
		if(result.status){
			total_kanban = result.total_kanbans;

			var hasil = [];
			total_kanban.reduce(function(res, value) {
				if (!res[value.product_gmc]) {
					res[value.product_gmc] = { product_gmc: value.product_gmc, qty: 0 };
					hasil.push(res[value.product_gmc])
				}
				res[value.product_gmc].qty += value.num;
				return res;
			}, {});

			$('#tableMaster').DataTable().clear();
			$('#tableMaster').DataTable().destroy();
			$('#tableMasterBody').empty();
			var bodyResume = "";

			actual_data = result.act_data;

			for (var i = 0; i < result.datas.length; i++) {
				if ((result.datas[i]['location'] == lokasi)) {
					bodyResume += '<tr style="cursor:pointer;" onclick="check_detail(\''+result.datas[i]['material_number']+'\')">';
					bodyResume += '<td>'+result.datas[i]['material_number']+'</td>';
					bodyResume += '<td style="text-align:left;padding-left:5px">'+result.datas[i]['material_description']+'</td>';
					bodyResume += '<td>'+(result.datas[i]['location'] || '')+'</td>';

					total_plan = Math.ceil(result.datas[i]['quantity']*2/result.datas[i]['lot']) + Math.ceil((result.datas[i]['lead_time']*result.datas[i]['quantity']/420)/result.datas[i]['lot']);

					var stat_total = false;
					var total_kbn = 0;

					$.each(hasil, function(index2, value2){
						if (value2.product_gmc == result.datas[i]['material_number']) {
							bodyResume += '<td onclick="openModalKanban(\''+result.datas[i]['material_number']+'\', \''+result.datas[i]['material_description']+'\',\''+result.datas[i]['location']+'\')">'+value2.qty+'</td>';
							stat_total = true;
							total_kbn = value2.qty;
						}
					})

					if (!stat_total) {
						bodyResume += '<td>0</td>';
					}

					bodyResume += '<td>'+total_plan+'</td>';
					bodyResume += '<td>'+result.datas[i]['actual_kanban']+'</td>';
					bodyResume += '<td>'+(parseInt(result.datas[i]['actual_kanban']) - total_plan)+'</td>';
					bodyResume += '<td>'+(total_plan - total_kbn)+'</td>';
					bodyResume += '</tr>';
				}
			}

			$('#tableMasterBody').append(bodyResume);

			var table = $('#tableMaster').DataTable({
				'dom': 'Bfrtip',
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
				'pageLength': 25,
				'searching'   	: true,
				'ordering'		: true,
				'order': [],
				'info'       	: true,
				'autoWidth'		: false,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
			});

		}
		else{
			openErrorGritter('Error!', 'Loading Failed.');
			audio_error.play();
		}
	});
}

function openModalKanban(gmc, desc, loc) {
	$("#detailKanban").modal('show');

	$("#kanban").text(gmc+' - '+desc);
	$("#bodyDetail").empty();
	body = '';

	var data = {
		material_number : gmc,
		location : loc,
	}

	$.get('{{ url("fetch/tpro/status_kanban") }}', data, function(result, status, xhr){
		if (result.status) {
			var no = 1;
			$(total_kanban).each(function(index2, value2) {
				var stat_total = false;
				$(result.datas).each(function(index, value) {
					if (value2.kartu_code == value.kanban_code) {
						stat_total = true;
						body += '<tr>';
						body += '<td>'+no+'</td>';
						body += '<td>'+value.kanban_number+'</td>';

						var color = '';
						if (value.status == 'actual') {
							color = 'style="background-color: #00a65a; color: white"';
						} else {
							color = 'style="background-color: #dd4b39; color: white"';
						}
						body += '<td '+color+'>'+value.status+'</td>';
						body += '<td>'+value.updated_at+'</td>';
						body += '</tr>';
						no++;
					}
				})

				if (!stat_total && value2.product_gmc == gmc) {
					body += '<tr>';
					body += '<td>'+no+'</td>';
					body += '<td>'+value2.kartu_no+'</td>';

					var color = '';
							// if (value.status == 'actual') {
							// 	color = 'style="background-color: #00a65a; color: white"';
							// } else {
							// 	color = 'style="background-color: #dd4b39; color: white"';
							// }
							body += '<td '+color+'>Belum Scan</td>';
							body += '<td>-</td>';
							body += '</tr>';
							no++;
						}
					})

			$("#bodyDetail").append(body);
		} else {
			openErrorGritter('Gagal', result.message);
		}
	})

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