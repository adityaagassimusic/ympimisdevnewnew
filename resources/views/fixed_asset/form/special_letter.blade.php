@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:left;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">	
	<h1>
		Form Special Letter of CIP Asset
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<center><h4 style="font-weight: bold">特　命　理　由　書</h4></center>
							<center><h4 style="font-weight: bold">Special Reason Letter</h4></center>
							<p>1. Japan (Version)</p>
							<p>I apply to inform reason  for Long Outstanding Contruction On Progres (CIP) as bellow :</p>
						</div>
						<div class="col-xs-12">
							<table class="table table-bordered" id="table_cip">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 10%">Fixed Asset Number</th>
										<th style="width: 25%">Fixed Asset Name</th>
										<th style="width: 10%">Date Acquisition</th>
										<th style="width: 8%">Amount</th>
										<th style="width: 8%">Old Plan Use</th>
										<th style="width: 8%">Plan Use</th>
										<th style="width: 1%">Action</th>
									</tr>
								</thead>
								<tbody id="body_cip"></tbody>
							</table>
						</div>
					</div>
				<!-- 	<div class="row">
						<div class="col-xs-2">
							2. 件名 <br>
							Subject<span class="text-red">*</span>
						</div>
						<div class="col-xs-8">
							<input type="text" class="form-control" id="subject_jp" placeholder="Subject (Japan Version)">
							<input type="text" class="form-control" id="subject" placeholder="Subject">
						</div>
					</div> -->

					<div class="row" style="margin-top: 10px">
						<div class="col-xs-2">
							2. 特命理由 <br>
							Specific Reason<span class="text-red">*</span>
						</div>
						<div class="col-xs-8">
							<textarea class="form-control" id="reason_jp" placeholder="Reason (Japan Version)"></textarea>
							<textarea class="form-control" id="reason" placeholder="Reason"></textarea>
						</div>
					</div>

					<div class="row" style="margin-top: 10px">
						<div class="col-xs-12">
							<button class="btn btn-success btn-md pull-right" onclick="save_form()"><i class="fa fa-check"></i> Submit & Send Approval</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

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

	var assets = <?php echo json_encode($assets); ?>;
	var clasic = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.plan_use').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchAsset();
	});

	$(function () {
		$('.select2').select2({
			allowClear : true
		});
	})

	function fetchAsset() {
		var body = '';

		$('#table_cip').DataTable().clear();
		$('#table_cip').DataTable().destroy();
		$("#body_cip").empty();

		$.each(assets, function(key, value) {
			body += '<tr>';
			body += '<td style="text-align: right" class="fa_num">'+value.fixed_asset_number+'</td>';
			body += '<td>'+value.fixed_asset_name+'</td>';
			body += '<td>'+value.acquisition_date+'</td>';
			body += '<td style="text-align: right">$ '+value.amount+'</td>';
			body += '<td>'+value.old_plan_use+'</td>';
			body += '<td>'+value.plan_use+'</td>';
			body += '<td><center><button class="btn btn-danger btn-sm" onclick="delete_row(this)"><i class="fa fa-minus"></i></button></center></td>';
			body += '</tr>';
		});

		$("#body_cip").append(body);

		$('.plan_use').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		// var table = $('#table_cip').DataTable({
		// 	'dom': 'Bfrtip',
		// 	'responsive':true,
		// 	'lengthMenu': [
		// 	[ 10, 25, 50, -1 ],
		// 	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		// 	],
		// 	'buttons': {
		// 		buttons:[
		// 		{
		// 			extend: 'pageLength',
		// 			className: 'btn btn-default',
		// 		},
		// 		]
		// 	},
		// 	'paging': false,
		// 	'lengthChange': false,
		// 	'searching': true,
		// 	'ordering': true,
		// 	'info': true,
		// 	'autoWidth': true,
		// 	"sPaginationType": "full_numbers",
		// 	"bJQueryUI": true,
		// 	"bAutoWidth": false,
		// 	"processing": true,
		// });
	}

	function delete_row(elem) {
		$(elem).closest('tr').remove();
	}

	function save_form() {
		$("#loading").show();
		var fa_number = [];

		$('.fa_num').each(function(i, obj) {
			fa_number.push($(obj).text());
		});

		if ($('.fa_num').length < 1) {
			openErrorGritter('Error', 'Please Add Asset');
			return false;
		}

		var formData = new FormData();
		formData.append('form_number', "{{ Request::segment(4) }}");
		formData.append('fixed_asset_number',  fa_number);
		formData.append('subject',  $("#subject").val());
		formData.append('subject_jp',  $("#subject_jp").val());
		formData.append('reason',  $("#reason").val());
		formData.append('reason_jp',  $("#reason_jp").val());

		$.ajax({
			url: '{{ url("post/fixed_asset_sp_letter/create") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				openSuccessGritter('Success', 'Special Letter Form Successfully Sent');

				// drawData();

			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
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