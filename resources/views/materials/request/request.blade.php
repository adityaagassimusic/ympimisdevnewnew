@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-7">
			<div class="form-group" style="margin-bottom: 5px;">
				<select class="form-control select2" onchange="fetchList(value)" name="location" id="location" data-placeholder="Pilih Lokasi" style="width: 40%;">
					<option></option>
					@foreach($storage_locations as $storage_location)
					<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }} - {{ $storage_location->location }}</option>
					@endforeach
				</select>
			</div>
			<div class="box">
				<div class="box-body">
					<table class="table table-hover table-striped" id="tableList">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Material Number</th>
								<th style="width: 5%;">Description</th>
								<th style="width: 1%;">Kirim</th>
								<th style="width: 1%;">Terima</th>
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
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-5">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-8">
							<span style="font-weight: bold; font-size: 16px;">Material:</span>
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-4">
							<span style="font-weight: bold; font-size: 16px;">Lot:</span>
							<input type="text" id="lot" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Lokasi Pengirim:</span>
							<input type="text" id="issue" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Lokasi Penerima:</span>
							<input type="text" id="receive" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Jumlah Permintaan:</span>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-7">
							<div class="input-group">
								<input id="quantity" style="font-size: 2.5vw; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">
							</div>
						</div>
						<div class="col-xs-5" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="requestMaterial()" style="font-size: 2vw; width: 100%; font-weight: bold; padding: 0;">
								<i class="fa fa-send-o"></i> REQUEST
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	function fetchRequest(material, description, issue, receive, lot){
		$('#material_number').val(material);
		$('#material_description').val(description);
		$('#issue').val(issue);
		$('#receive').val(receive);
		$('#lot').val(lot);
	}

	function fetchList(id){
		var data = {
			location:id
		}
		$.get('{{{ url("fetch/material/request_list") }}}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableBody = "";
				var count = 1;
				$('#tableBodyList').html("");

				$.each(result.lists, function(key, value){
					tableBody += '<tr onclick="fetchRequest(\''+value.material_number+'\''+','+'\''+value.description+'\''+','+'\''+value.issue_location+'\''+','+'\''+value.receive_location+'\''+','+'\''+value.lot+'\')">';
					tableBody += '<td>'+count+'</td>';
					tableBody += '<td>'+value.material_number+'</td>';
					tableBody += '<td>'+value.description+'</td>';
					tableBody += '<td>'+value.issue_location+'</td>';
					tableBody += '<td>'+value.receive_location+'</td>';
					tableBody += '</tr>';
					count += 1;
				});
				$('#tableBodyList').append(tableBody);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var tableList = $('#tableList').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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
						'lengthChange': true,
						'pageLength': 20,
						'searching': true,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

				tableList.columns().every( function () {
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

				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
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