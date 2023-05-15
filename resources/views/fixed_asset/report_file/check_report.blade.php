@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		vertical-align: middle;
		text-align: center;
		font-size:  1vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		font-size:  1vw;
		padding: 3px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#body_asset > tr > td {
		background-color: #fff;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of Asset Check {{ Request::segment(4) }} , Period {{ Request::segment(5) }} 
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="box no-border">
				<div class="box-body">
					<div class="row" style="overflow-x: scroll;">

						<div class="col-xs-12">
							<br>
							<table id="AuditAssetTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th rowspan="2">FA Number</th>
										<th rowspan="2">Fixed Asset Name</th>
										<th rowspan="2">Location</th>
										<th rowspan="2">Reff Image</th>
										<th rowspan="2">Check Image</th>
										<th rowspan="2">Keberadaan</th>
										<th colspan="4">Kondisi Pengecualian</th>
										<th rowspan="2">Note</th>
										<th colspan="3">TTD</th>
									</tr>
									<tr>
										<th>Tidak Digunakan</th>
										<th>Asset Rusak</th>
										<th>Label Rusak</th>
										<th>MAP tidak sesuai</th>
										<th>Cek 1</th>
										<th>Cek 2</th>
										<th>Audit</th>
									</tr>
								</thead>
								<tbody id="AuditAssetBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

	<div class="modal fade" id="modalImage">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
										CLOSE
									</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="images" style="padding-top: 20px">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
var vendor_arr = [];

jQuery(document).ready(function() {
	$('body').toggleClass("sidebar-collapse");

	$('.monthpicker').datepicker({
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		todayHighlight: true
	});

	$('.select2').select2({
		// dropdownParent: $("#generateModal")
		dropdownPosition: 'below'
	});

	getData();
});

function getData() {
	$("#loading").show();
	var data = {
		period : '{{ Request::segment(5) }}',
		section : '{{ Request::segment(4) }}'
	}

	$.get('{{ url("fetch/fixed_asset/check/list") }}', data, function(result, status, xhr) {
		$("#loading").hide();
		// $('#AuditAssetTable').DataTable().clear();
		// $('#AuditAssetTable').DataTable().destroy();
		$("#AuditAssetBody").empty();
		body = "";

		$.each(result.assets, function(index, value){
			body += "<tr>";			
			body += "<td>"+value.sap_number+"</td>";
			body += "<td>"+value.asset_name+"</td>";
			body += "<td>"+value.location+"</td>";
			var url = "{{ url('files/fixed_asset/asset_picture') }}/"+value.asset_images;
			body += "<td><img src='"+url+"' style='max-width: 70px; max-height: 70px; cursor:pointer' onclick='modalImage(\""+url+"\")' Alt='Image Not Found'></td>";

			var url2 = "{{ url('files/fixed_asset/asset_check') }}/"+value.result_images;
			body += "<td><img src='"+url2+"' style='max-width: 70px; max-height: 70px; cursor:pointer' onclick='modalImage(\""+url2+"\")' Alt='Image Not Found'></td>";
			

			body += "<td>";
			if (value.availability == 'Ada') {
				body += "<label class='btn btn-success btn-xs' ><i class='fa fa-check'></i> Ada</label>";
			} else {
				body += "<label class='btn btn-danger btn-xs'><i class='fa fa-close'></i> Tidak Ada</label>";
			}
			body += "</td>";

			body += "<td style='background-color: #f5e9ab'>";
			if (value.usable_condition == 'Tidak Digunakan') {
				body += "<label class='btn btn-danger btn-xs'><i class='fa fa-check'></i> </label>";
			}

			body += "</td>";

			body += "<td style='background-color: #f5e9ab'>";
			if (value.asset_condition == 'Rusak') {
				body += "<label class='btn btn-danger btn-xs'><i class='fa fa-check'></i> </label>";
			}

			body += "</td>";

			body += "<td style='background-color: #f5e9ab'>";
			if (value.label_condition == 'Rusak') {
				body += "<label class='btn btn-danger btn-xs'><i class='fa fa-check'></i> </label>";
			}

			body += "</td>";

			body += "<td style='background-color: #f5e9ab'>";
			if (value.map_condition == 'Tidak Sesuai') {
				body += "<label class='btn btn-danger btn-xs'><i class='fa fa-close'></i> </label>";
			}

			body += "</td>";

			body += "<td>"+(value.note || '')+"</td>";
			body += "<td>"+(value.check_one_by || '')+"</td>";
			body += "<td>"+(value.check_two_by || '')+"</td>";
			body += "<td></td>";

			body += "</tr>";
		})

		$("#AuditAssetBody").append(body);


		var table = $('#AuditAssetTable').DataTable({
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
				]
			},
			'paging': true,
			'lengthChange': true,
			"pageLength": 100,
			'searching': true,
			'ordering': true,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
		});
	})
}

function modalImage(url) {
	$('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
	$('#modalImage').modal('show');
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
	audio_error.play();
}

</script>
@endsection