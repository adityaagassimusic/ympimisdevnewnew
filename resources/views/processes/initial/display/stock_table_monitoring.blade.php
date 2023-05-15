@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #212121;  
		color:white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(54, 59, 56);
		background-color: #212121;
		color: white;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}

	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}

	table.table-striped > thead > tr > th{
		border:1px solid black !important;
		text-align: center;
		background-color: rgba(126,86,134,.7) !important;  
	}

	table.table-striped > tbody > tr > td{
		border: 1px solid #eeeeee !important;
		border-collapse: collapse;
		color: black;
		padding: 3px;
		vertical-align: middle;
		text-align: center;
		background-color: white;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table > thead > tr > th{
		border:2px solid #f4f4f4;
		color: white;
	}

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<input type="hidden" id="location" value="{{ $location }}">
			<table id="tableList" class="table table-bordered" style="width: 100%;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>No</th>
						<th>Material</th>
						<th>Description</th>
						<th>Remark</th>
						<th>Stock/Day</th>
						<th>Act. Stock</th>
						<th>Stock</th>						
					</tr>
				</thead>
				<tbody id="tableBodyList">
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
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</section>
@endsection
@section('scripts')
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

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
	});

	function fillChart(){
		var location = $('#location').val();
		
		var data = {
			location : location,
		}
		$.get('{{ url("fetch/initial/stock_monitoring_table") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$("#tableBodyList").empty();
				var body = '';

				$.each(result.stocks, function(key, value) {
					var color = '';
					if (value.category == '0Days' || value.category == '<0.5Days' || value.category == '<1Days' ) {
						color = 'style = "background-color:rgba(255, 97, 131, 0.9)";';
					} else if (value.category == '<1.5Days' || value.category == '<2Days') {
						color = 'style = "background-color:rgba(255, 213, 97, 0.9)"';
					} else if (value.category == '<2.5Days' || value.category == '<3Days' || value.category == '<3.5Days' || value.category == '<4Days') {
						color = 'style = "background-color:rgba(81, 212, 76, 0.9)"';
					} else if (value.category == '<4.5Days' || value.category == '>4.5Days') {
						color = 'style = "background-color:rgba(139, 97, 255, 0.9)"';
					}

					

					body += '<tr>';
					body += '<td>'+ (key+1) +'</td>';
					body += '<td>'+ value.material_number +'</td>';
					body += '<td>'+ value.description +'</td>';
					body += '<td>'+ value.remark +'</td>';
					body += '<td>'+ value.safety.toLocaleString() +'</td>';
					body += '<td>'+ parseInt(value.quantity).toLocaleString() +'</td>';
					body += '<td '+color+'>'+ value.category +'</td>';
					body += '</tr>';
				});

				$("#tableBodyList").append(body);

				$('#tableList tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center; color: black" type="text" placeholder="Search '+title+'" size="20"/>' );
				} );

				var table = $('#tableList').DataTable({
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
						},
						]
					},
					'paging': true,
					'pageLength': 50,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
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

				$('#tableList tfoot tr').appendTo('#tableList thead');
			}
		});
	}

</script>
@endsection