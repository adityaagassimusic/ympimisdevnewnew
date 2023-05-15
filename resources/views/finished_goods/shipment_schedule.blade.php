@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	input {
		line-height: 24px;
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
	#loading, #error { display: none; }
	
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Shipment Schedule Data <span class="text-purple">出荷スケジュール</span>
		{{-- <small>Material stock details <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update"></ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Period From</label>
								<select class="form-control select2" name="periodFrom" id='periodFrom' data-placeholder="Select Period" style="width: 100%;">
									<option></option>
									@foreach($periods as $period)
									<option value="{{ $period->st_month }}">{{ date('F Y', strtotime($period->st_month)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Period To</label>
								<select class="form-control select2" name="periodTo" id='periodTo' data-placeholder="Select Period" style="width: 100%;">
									<option></option>
									@foreach($periods as $period)
									<option value="{{ $period->st_month }}">{{ date('F Y', strtotime($period->st_month)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Origin Group" name="origin_group" id="origin_group">
									<option></option>
									@foreach($origin_groups as $origin_group)
									<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Work Center" name="hpl" id="hpl" style="width: 100%;">
									<option></option>
									@foreach($hpls as $hpl)
									<option value="{{ $hpl->hpl }}">{{ $hpl->category }} - {{ $hpl->hpl }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Category" name="category" id="category" style="width: 100%;">
									<option></option>
									@foreach($categories as $category)
									<option value="{{ $category }}">{{ $category }}<option>
										@endforeach
									</select>
								</div>
								<div class="form-group pull-right">
									<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
									<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<table id="shipmentScheduleTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 1%;">Period</th>
											<th style="width: 1%;">Cat.</th>
											<th style="width: 1%;">Sales Order</th>
											<th style="width: 1%;">Dest</th>
											<th style="width: 1%;">By</th>
											<th style="width: 5%;">Material</th>
											<th style="width: 30%;">Desc</th>
											<th style="width: 1%;">HPL</th>
											<th style="width: 1%;">Plan</th>
											<th style="width: 1%;">Act Prod.</th>
											<th style="width: 1%;">Diff</th>
											<th style="width: 1%;">Act Deliv.</th>
											<th style="width: 1%;">Diff</th>
											<th style="width: 10%;">Ship. Date</th>
											<th style="width: 10%;">BL Date Plan</th>
										</tr>
									</thead>
									<tbody id="tableBody">
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
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('.select2').select2();
			fillTable();
		});

		function clearConfirmation(){
			location.reload(true);
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

		function fillTable(){
			var periodTo = $('#periodTo').val();
			var periodFrom = $('#periodFrom').val();
			var originGroupCode = $('#origin_group').val();
			var hpl = $('#hpl').val();
			var category = $('#category').val();
			var data = {
				periodTo:periodTo,
				periodFrom:periodFrom,
				originGroupCode:originGroupCode,
				hpl:hpl,
				category:category,
			}

			$('#loading').show();
			$.get('{{ url("fetch/fg_shipment_schedule") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
						$('#shipmentScheduleTable').DataTable().clear();
						$('#shipmentScheduleTable').DataTable().destroy();


						$('#shipmentScheduleTable thead').html("");
						var head = '';
						head += '<tr>';
						head += '<th style="width: 1%;">ID</th>';
						head += '<th style="width: 1%;">Period</th>';
						head += '<th style="width: 1%;">Cat.</th>';
						head += '<th style="width: 1%;">Sales Order</th>';
						head += '<th style="width: 1%;">Dest</th>';
						head += '<th style="width: 1%;">By</th>';
						head += '<th style="width: 5%;">Material</th>';
						head += '<th style="width: 30%;">Desc</th>';
						head += '<th style="width: 1%;">HPL</th>';
						head += '<th style="width: 1%;">Plan</th>';
						head += '<th style="width: 1%;">Act Prod.</th>';
						head += '<th style="width: 1%;">Diff</th>';
						head += '<th style="width: 1%;">Act Deliv.</th>';
						head += '<th style="width: 1%;">Diff</th>';
						head += '<th style="width: 10%;">Ship. Date</th>';
						head += '<th style="width: 10%;">BL Date Plan</th>';
						head += '</tr>';						
						$('#shipmentScheduleTable thead').append(head);


						$('#shipmentScheduleTable tfoot').html("");
						var foot = '';
						foot += '<tr>'
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '<th></th>';
						foot += '</tr>';
						$('#shipmentScheduleTable tfoot').append(foot);


						$('#tableBody').html("");
						var tableData = '';
						$.each(result.tableData, function(key, value) {
							tableData += '<tr>';
							tableData += '<td>'+ value.id +'</td>';
							tableData += '<td>'+ value.st_month +'</td>';
							tableData += '<td>'+ value.category +'</td>';
							tableData += '<td>'+ (value.sales_order || '') +'</td>';
							tableData += '<td>'+ value.destination_shortname +'</td>';
							tableData += '<td>'+ value.shipment_condition_name +'</td>';
							tableData += '<td>'+ value.material_number +'</td>';
							tableData += '<td>'+ value.material_description +'</td>';
							tableData += '<td>'+ value.hpl +'</td>';
							tableData += '<td>'+ value.quantity +'</td>';
							tableData += '<td>'+ value.quantity_production +'</td>';
							tableData += '<td>'+ (value.quantity_production-value.quantity) +'</td>';
							tableData += '<td>'+ value.quantity_delivery +'</td>';
							tableData += '<td>'+ (value.quantity_delivery-value.quantity) +'</td>';
							tableData += '<td>'+ value.st_date +'</td>';
							tableData += '<td>'+ value.bl_date_plan +'</td>';
							tableData += '</tr>';		
						});
						$('#tableBody').append(tableData);

						$('#shipmentScheduleTable tfoot th').each( function () {
							var title = $(this).text();
							$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
						} );

						var table = $('#shipmentScheduleTable').DataTable({
							'dom': 'Bfrtip',
							'responsive': true,
							'lengthMenu': [
							[ 10, 25, 50, -1 ],
							[ '10 rows', '25 rows', '50 rows', 'Show all' ]
							],
							"pageLength": 25,
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
							'searching': true,
							'ordering': true,
							'order': [],
							'info': true,
							'autoWidth': true,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
							"bAutoWidth": false,
							"columnDefs": [ {
								"targets": [11, 13],
								"createdCell": function (td, cellData, rowData, row, col) {
									if ( cellData <  0 ) {
										$(td).css('background-color', 'RGB(255,204,255)')
									}else{
										$(td).css('background-color', 'RGB(204,255,255)')
									}
								}
							}]
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
						$('#shipmentScheduleTable tfoot tr').prependTo('#shipmentScheduleTable thead');

						$('#loading').hide();

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


</script>
@endsection