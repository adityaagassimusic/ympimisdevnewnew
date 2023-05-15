@extends('layouts.master')
@section('stylesheets')
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
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
									@foreach($locations as $location) 
									<option value="{{ $location }}">{{ $location }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillList()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>			
					<div class="row">
						<div class="col-md-12" id="tableMasterContainer">
							
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

		$('#datefrom').datepicker({
			autoclose: true
		});
		$('#dateto').datepicker({
			autoclose: true
		});
		$('.select2').select2({
		});
	});

	fillList();

	function clearConfirmation(){
		location.reload(true);		
	}

	function tableInit() {
		$('#tableMasterContainer').html("");
		var table = "";

		table += '<table id="report" class="table table-bordered table-striped table-hover">';
		table += '<thead style="background-color: rgba(126,86,134,.7);">';
		table += '<tr>';
		table += '<th style="width: 8%">NIK</th>';
		table += '<th style="width: 10%">Name</th>';
		table += '<th style="width: 8%">Tag</th>';
		table += '<th style="width: 8%">Material Number</th>';
		table += '<th style="width: 15%">Material Description</th>';
		table += '<th style="width: 3%">Type</th>';
		table += '<th style="width: 3%">Model</th>';
		table += '<th style="width: 5%">NG name</th>';
		table += '<th style="width: 5%">Qty</th>';
		table += '<th style="width: 7%">location</th>';
		table += '<th style="width: 12%">Created at</th>';
		table += '</tr>';
		table += '</thead>';
		table += '<tbody id="tableBodyMaster">';
		table += '</tbody>';
		table += '<tfoot style="background-color: RGB(252, 248, 227);">';
		table += '<tr>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '<th></th>';
		table += '</tr>';
		table += '</tfoot>';
		table += '</table>';

		$('#tableMasterContainer').append(table);

		// <table id="report" class="table table-bordered table-striped table-hover">
		// <thead style="background-color: rgba(126,86,134,.7);">
		// <tr>
		// <th style="width: 8%">NIK</th>
		// <th style="width: 10%">Name</th>
		// <th style="width: 8%">Tag</th>
		// <th style="width: 8%">Material Number</th>
		// <th style="width: 15%">Material Description</th>
		// <th style="width: 3%">Type</th>
		// <th style="width: 3%">Model</th>										
		// <th style="width: 5%">NG name</th>
		// <th style="width: 5%">Qty</th>
		// <th style="width: 7%">location</th>
		// <th style="width: 12%">Created at</th>
		// </tr>
		// </thead>
		// <tbody id="bodyTableNGReport">
		// </tbody>
		// <tfoot style="background-color: RGB(252, 248, 227);">
		// <tr>
		// <th></th>
		// <th></th>
		// <th></th>
		// <th></th>
		// <th></th>
		// <th></th>										
		// <th></th>
		// <th></th>
		// <th></th>
		// <th></th>
		// <th></th>
		// </tr>
		// </tfoot>
		// </table>		
	}

	function fillList(){
		tableInit();
		$('#loading').show();
		
		let id = $('#location').val().substr($('#location').val().length - 2);		

		var data = {				
			id:id,
			material_category:$('#material_category').val(),
			material:$('#material').val(),
			material_type:$('#material_type').val(),
			location:$('#location').val(),
		}
		$.get('{{ url("fetch/body_parts_process/report_ng") }}',data, function(result, status, xhr){
			if(result.status){
				$('#report').DataTable().clear();
				$('#report').DataTable().destroy();
				$('#bodyTableNGReport').html("");
				var tableData = "";				
				var index = 1;		
				var all_kanban = [];										

				$.each(result.value, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tag +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.key +'</td>';
					tableData += '<td>'+ value.model +'</td>';					
					tableData += '<td>'+ value.ng_name +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.location +'</td>';
					tableData += '<td>'+ value.created_at +'</td>';
					tableData += '</tr>';
					index += 1;
				});

				safety = result.safety;
				$('#report').append(tableData);					

				var table = $('#report').DataTable({
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
					'pageLength': 10,
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

				$('#report tfoot th').each( function () {
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
				$('#report tfoot tr').appendTo('#report thead');

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}		

</script>
@endsection