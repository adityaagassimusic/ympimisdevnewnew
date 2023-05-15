@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		
	}
	tbody>tr>td{
		
	}
	tfoot>tr>th{
		
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
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters <span class="text-purple"></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Prod. Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" placeholder="Select Date">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Prod. Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" name="dateto" placeholder="Select Date">
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Location" name="location" id="location" style="width: 100%;">
									<option value="">Select Location</option>
									@foreach($locations as $location) 
									<option value="{{ $location }}">{{ $location }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>			
					<div class="row">
						<div class="col-md-12">
							<table id="tableReport" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 2%">No.</th>
										<th style="width: 8%">NIK</th>
										<th style="width: 10%">Name</th>
										<th style="width: 8%">Tag</th>
										<th style="width: 8%">Material Number</th>
										<th style="width: 15%">Material Description</th>
										<th style="width: 3%">Key</th>
										<th style="width: 3%">Model</th>
										<th style="width: 4%">Surface</th>
										<th style="width: 5%">Qty</th>
										<th style="width: 7%">location</th>
										<th style="width: 12%">Created at</th>
									</tr>
								</thead>
								<tbody id="bodyTableReport">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
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
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			todayHighlight : true,
			autoclose: true,
			format: "yyyy-mm-dd",
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#dateto').datepicker({
			<?php $tgl_max = date('Y-m-d',strtotime("+ 1 days")) ?>
			todayHighlight : true,
			autoclose: true,
			format: "yyyy-mm-dd",
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#loading').show();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var location = $('#location').val();

		if(datefrom == "" || dateto == "" || location == ""){
			$('#loading').hide();
			openErrorGritter('Error!',"Masukkan tanggal dan lokasi");
			return false;
		}

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			location:location
		}

		$.get('{{ url("fetch/welding/production_result") }}', data, function(result, status, xhr){
			if (result.status) {
				$('#tableReport').DataTable().clear();
				$('#tableReport').DataTable().destroy();
				$('#bodyTableReport').html("");
				var tableData = "";
				var index = 1;
				if (location == 'hts-stamp-sx') {
					$.each(result.datas, function(key, value) {
						tableData += '<tr>';
						tableData += '<td style="text-align:center;">'+ index +'</td>';
						var user_id = '';
						var user_name = '';
						for(var i = 0; i < result.user.length;i++){
							if (result.user[i].id == value.created_by) {
								user_id = result.user[i].username.toUpperCase();
								user_name = result.user[i].name;
							}
						}
						tableData += '<td style="text-align:left;padding-left:4px;">'+ user_id +'</td>';
						tableData += '<td style="text-align:left;padding-left:4px;">'+ user_name +'</td>';
						tableData += '<td style="text-align:left;padding-left:4px;">'+value.serial_number+'</td>';
						tableData += '<td style="text-align:center;">-</td>';
						tableData += '<td style="text-align:center;">-</td>';
						tableData += '<td style="text-align:center;">-</td>';
						tableData += '<td style="text-align:left;padding-left:4px;">'+value.model+'</td>';
						tableData += '<td style="text-align:center;">-</td>';
						tableData += '<td style="text-align:center;">'+value.quantity+'</td>';
						tableData += '<td style="text-align:left;padding-left:4px;">'+location+'</td>';
						tableData += '<td style="text-align:right;padding-right:4px;">'+value.created_at+'</td>';
						tableData += '</tr>';
						index++;
					});
				}else{
					$.each(result.datas, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ index +'</td>';
						tableData += '<td>'+ value.last_check +'</td>';
						var name = '';
						for(var i = 0; i < result.emp.length;i++){
							if (result.emp[i].employee_id == value.last_check) {
								name = result.emp[i].name;
							}
						}
						tableData += '<td>'+ name +'</td>';
						tableData += '<td>'+ value.tags +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.key +'</td>';
						tableData += '<td>'+ value.model +'</td>';
						tableData += '<td>'+ value.surface +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.location +'</td>';
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '</tr>';
						index++;
					});
				}
				$('#bodyTableReport').append(tableData);

				var table = $('#tableReport').DataTable({
					'dom': 'Bfrtip',
					'responsive': true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
							// text: '<i class="fa fa-print"></i> Show',
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
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					// "serverSide": true,
				});

				$('#tableReport tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
				});

				table.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					});
				});
				$('#tableReport tfoot tr').appendTo('#tableReport thead');

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error',result.message);
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