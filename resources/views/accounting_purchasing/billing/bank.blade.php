@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box">
		<div class="box-body" style="overflow-x:scroll;">
			<div class="row">
				
			</div>

			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>Vendor Name</th>
                        <th>Currency</th>
                        <th>Bank & Branch Name</th>
                        <th>Nama Rekening</th>
                        <th>No Rekening</th>
                        <th>Internal</th>
                        <th>LN</th>
                        <th>Not Active</th>
                        <th>Vendor Address</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Bank Address</th>
                        <th>Bank Name</th>
                        <th>Bank Branch</th>
                        <th>Bank City Country</th>
                        <th>Switch Code</th>
                        <th>Full Amount</th>
                        <th>Sett Acc No</th>
                        <th>Sector Select</th>
                        <th>Resident</th>
                        <th>Citizenship</th>
                        <th>Relation</th>
                        <th>Bank Charge</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
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

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
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
		fetchTable();
	});

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearNew(){
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/bank") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.bank, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="text-align:left">'+value.vendor+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.currency+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.branch+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.rekening_no+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.rekening_nama+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.internal+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.ln+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.not_active+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.address_vendor+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.city+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.country+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.bank_address+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.bank_name+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.bank_branch+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.bank_city_country+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.switch_code+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.full_amount+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.settle_acc_no+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.sector_select+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.resident+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.citizenship+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.relation+'</td>';
                    listTableBody += '<td style="text-align:left">'+value.bank_charge+'</td>';
					listTableBody += '</tr>';


					count_all += 1;
				});

				// $('#count_all').text(count_all);

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
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

