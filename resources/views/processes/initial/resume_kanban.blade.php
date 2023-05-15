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
			<a data-toggle="modal" data-target="#uploadModal" class="btn btn-success" style="color:white"><i class="fa fa-upload"></i>Upload Target Bulanan</a>
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
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="col-xs-12">
			<div class="col-md-2" style="padding: 0">
				<div class="form-group">
					<label>Month</label>
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control pull-right" id="bulan" name="bulan" onchange="fillTable()" placeholder="select month">
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<table id="tableResume" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr style="border: 1px solid black">
						<th width="10%">GMC</th>
						<th width="10%">Description</th>
						<!-- <th width="8%">Target Per Bulan</th> -->
						<th width="8%">Target Daily</th>
						<th width="8%">Safety stock (2 hari)</th>
						<th width="8%">Lot Process</th>
						<th width="8%">SLoc</th>
						<th width="8%">Location</th>
						<th width="8%">Dandori Time</th>
						<th width="8%">Cycle Time</th>				
						<th width="8%">Total Time</th>				
						<th width="8%">Lead Time</th>			
						<th width="8%">Production Req</th>		
						<th width="8%">Safety Kanban</th>		
						<th width="8%">Jumlah Kanban</th>		
						<th width="8%">Total Kanban</th>		
					</tr>
				</thead>
				<tbody id="tableResumeBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
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
</section>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form id="uploadTarget">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><center><b>Upload Target Bulanan</b></center></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-sm-12 control-label">Target Bulan :</label>

								<div class="col-sm-12">
									<input type="text" class="form-control bulan" id="bulan_upload" placeholder="Pilih bulan" name="bulan">
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="form-group">
								<label class="col-sm-12 control-label">File :</label>

								<div class="col-sm-12">
									<input type="file" name="file_upload" id="file_upload">
								</div>
							</div>
						</div>
					</div>    
				</div>
				<div class="modal-footer">
					<div class="row" style="margin-left: 2%; margin-right: 2%;">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
						<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Upload</button>
					</div>
				</div>
			</form>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillTable();

		$('#bulan').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});

		$('.bulan').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});
	});	


	function fillTable(){
		$("#loading").show();

		var bulan = $('#bulan').val();

		var data = {
			bulan:bulan
		}

		$.get('{{ url("fetch/tpro/resume_kanban") }}',data, function(result, status, xhr){
			$("#loading").hide();
			if(result.status){
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				var bodyResume = "";
				$('#tableResumeBody').html("");

				for (var i = 0; i < result.datas.length; i++) {
					bodyResume += '<tr style="cursor:pointer;">';
					bodyResume += '<td>'+result.datas[i]['material_number']+'</td>';
					bodyResume += '<td style="text-align:left;padding-left:5px">'+result.datas[i]['material_description']+'</td>';
					// bodyResume += '<td>'+result.datas[i]['quantity']+'</td>';
					bodyResume += '<td>'+Math.ceil(result.datas[i]['quantity'])+'</td>';
					bodyResume += '<td>'+Math.ceil(result.datas[i]['quantity']*2)+'</td>';
					bodyResume += '<td>'+result.datas[i]['lot']+'</td>';
					bodyResume += '<td>'+(result.datas[i]['sloc'] || '')+'</td>';
					bodyResume += '<td>'+(result.datas[i]['location'] || '')+'</td>';
					bodyResume += '<td>'+parseFloat(result.datas[i]['dandori_time']).toFixed(2)+'</td>';
					bodyResume += '<td>'+parseFloat(result.datas[i]['cycle_time']).toFixed(2)+'</td>';
					bodyResume += '<td>'+(
						parseFloat(parseFloat(result.datas[i]['dandori_time']) * parseFloat(result.datas[i]['lot'])) 
						+
						parseFloat(parseFloat(result.datas[i]['cycle_time']) * parseFloat(result.datas[i]['lot']))
						).toFixed(2)+'  </td>';
					bodyResume += '<td>'+result.datas[i]['lead_time']+'</td>';
					bodyResume += '<td>'+(result.datas[i]['quantity']/420).toFixed(1)+'</td>';

					var safety_kanban = Math.ceil(result.datas[i]['quantity']*2/result.datas[i]['lot']);
					var jumlah_kanban = Math.ceil((result.datas[i]['lead_time']*result.datas[i]['quantity']/420)/result.datas[i]['lot']);
					var total_kanban = safety_kanban + jumlah_kanban;

					if (total_kanban < 3) {
						total_kanban = 3;
					}

					bodyResume += '<td style="background-color:orange;color:black;font-size:20px;font-weight:bold">'+safety_kanban+'</td>';
					bodyResume += '<td style="background-color:blue;color:white;font-size:20px;font-weight:bold">'+jumlah_kanban+'</td>';
					bodyResume += '<td style="background-color:green;color:white;font-size:20px;font-weight:bold">'+total_kanban+'</td>';


					bodyResume += '</tr>';
				}

				$('#tableResumeBody').append(bodyResume);

				$('#tableResume tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableResume').DataTable({
					'dom': 'Bfrtip',
					'lengthMenu': [
					[ 20, 25, 50, -1 ],
					[ '20 rows', '25 rows', '50 rows', 'Show all' ]
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
					'paging'		: true,
					'lengthChange'	: true,
					'pageLength'	: 25,
					'searching'   	: true,
					'ordering'		: true,
					'order'			: [],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI"		: true,
					"bAutoWidth"	: false,
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

				$('#tableResume tfoot tr').appendTo('#tableResume thead');
			}
			else{
				openErrorGritter('Error!', 'Loading Failed.');
				audio_error.play();
			}
		});
}

$( "#uploadTarget" ).submit(function( event ) {

	var formData = new FormData();
	formData.append('mon', $("#bulan_upload").val());
	formData.append('file_upload', $('#file_upload').prop('files')[0]);


	$.ajax({
		url: '{{ url("send/fixed_asset/registration_asset_form") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

			$("#invoice_name").val("");
			$("#item_name").val("");
			$("#invoice_number").val("");
			$("#clasification").select2("val", "");
			$("#clasification_mid").select2("val", "");
			$("#investment_number").val("");
			$("#budget").val("");
			$("#vendor").val("");
			$("#currency").select2("val", "");
			$("#amount").val("");
			$("#amount_usd").val("");
			$("#location").val("");
			$("#pic").val("");
			$('input[name="usage_term"]').prop('checked', false);
			$("#usage_est").val("");


			$('#createModal').modal('hide');

			openSuccessGritter('Success', result.message);

				// location.reload(true);
				draw_data();

			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
});


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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