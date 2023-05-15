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

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
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
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif	
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>					
	<div class="row">
		<div class="col-xs-12 pull-left">
			<table id="tableInventories" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th>Material Number</th>
						<th>Material Description</th>
						<th>Location</th>
						<th>Quantity</th>
						<th>Updated At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableInventories">
				</tbody>
				<tfoot>
					<tr style="color: black">
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

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #fc9803; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Update Inventory</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="operator_id" id="operator_id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="material" placeholder="Material" readonly>
										<input type="hidden" class="form-control" id="id_inventory" placeholder="Tap ID Card Operator" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="loc" placeholder="Location" readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="quantity" placeholder="Qty" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()" style="width: 100%;font-weight: bold;font-size: 20px"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>
</section>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();
	});

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

	function fillList(){
		$('#loading').show();
		var data = {
			loc:'{{$loc}}'
		}
		$.get('{{ url("fetch/injection/inventories") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableInventories').DataTable().clear();
				$('#tableInventories').DataTable().destroy();
				$('#bodyTableInventories').html("");
				var tableData = "";
				$.each(result.datas, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.part_name +'</td>';
					tableData += '<td>'+ value.location +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.update_inventories +'</td>';
					tableData += '<td><button class="btn btn-warning btn-xs" onclick="updateInventory(\''+value.id_inventory+'\',\''+value.material_number+'\',\''+value.part_name+'\',\''+value.location+'\',\''+value.quantity+'\')">Edit</button></td>';
					tableData += '</tr>';
				});
				$('#bodyTableInventories').append(tableData);

				$('#tableInventories tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableInventories').DataTable({
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

				$('#tableInventories tfoot tr').appendTo('#tableInventories thead');

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!','Attempt to retrieve data failed');
				return false;
			}
		});
	}
	function updateInventory(id,material_number,material_description,location,quantity) {
		$('#loading').show();
		$('#id_inventory').val(id);
		$('#material').val(material_number+' - '+material_description);
		$('#loc').val(location);
		$('#quantity').val(quantity);
		$('#edit-modal').modal('show');
		$('#loading').hide();
	}

	function update() {
		if (confirm('Apakah Anda yakin akan mengubah quantity inventory?')) {
			$('#loading').show();
			if ($('#quantity').val() == '') {
				openErrorGritter('Error!','Quantity Tidak Boleh Kosong.');
				audio_error.play();
				$('#loading').hide();
				return false;
			}
			var data = {
				id:$('#id_inventory').val(),
				quantity:$('#quantity').val(),
			}
			$.get('{{ url("update/injection/inventories") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!',result.message);
					$('#edit-modal').modal('hide');
					$('#loading').hide();
					location.reload();
				}else{
					openSuccessGritter('Success!',result.message);
					audio_error.play();
					$('#loading').hide();
					return false;
				}
			});
		}
	}


</script>
@endsection