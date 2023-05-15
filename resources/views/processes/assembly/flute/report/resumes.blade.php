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
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
		padding-top: 0;
		padding-bottom: 0;
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
		Stamp Record ~ {{$title}} <small><span class="text-purple">刻印記録</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Stamp Record Filters <span class="text-purple">Stamp Record フィルター</span></h3>
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
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" placeholder="Prod. Date From">
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
									<input type="text" class="form-control pull-right" id="dateto" name="dateto"  placeholder="Prod. Date To">
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Process Code" name="code" id="code" style="width: 100%;">
									<option value=""></option>
									@foreach($code as $code) 
									<option value="{{ $code->process_name }}">{{ $code->process_code }} - {{ $code->process_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" class="form-control pull-right" id="serial_number" name="serial_number"  placeholder="Serial Number">
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fillFloDetail()" class="btn btn-primary">Search</button>
						</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);" id="headEx">
									<tr>
										<th style="width: 1%;">Serial Number</th>
										<th style="width: 2%;">Process</th>
										<th style="width: 1%;">Model</th>
										<th style="width: 1%;">Qty</th>
										<th style="width: 2%;">PIC</th>
										<th style="width: 2%;">Created At</th>
										<th style="width: 1%;">Status Material</th>
									</tr>
								</thead>
								<tbody id="example1Body">
								</tbody>
								<tfoot id="footEx">
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#dateto').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fetchHeadFoot() {
		$('#headEx').html('');
		$('#footEx').html('');
		var thead = '';
		var tfoot = '';

		thead += '<tr>';
		thead += '<th>Serial Number</th>';
		thead += '<th>Process</th>';
		thead += '<th>Model</th>';
		thead += '<th>Qty</th>';
		thead += '<th>PIC</th>';
		thead += '<th>Created At</th>';
		thead += '<th>Status Material</th>';
		thead += '</tr>';

		tfoot += '<tr>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
			tfoot += '<th></th>';
		tfoot += '</tr>';

		$('#headEx').append(thead);
		$('#footEx').append(tfoot);
	}

	function fillFloDetail(){
		$('#loading').show();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var code = $('#code').val();
		var serial_number = $('#serial_number').val();

		if (serial_number == '') {
			if (datefrom == '' || dateto == '' || code == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Semua Harus Diisi');
				return false;
			}
		}
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			code:code,
			serial_number:serial_number,
			origin_group_code:'{{$origin_group_code}}'
		}
		$.get('{{ url("fetch/assembly/stamp_record") }}',data, function(result, status, xhr){
			if(result.status){
				$('#example1').DataTable().clear();
				$('#example1').DataTable().destroy();
				fetchHeadFoot();
				$('#example1Body').html("");
				var tableData = "";
				$.each(result.stamp_detail, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.serial_number +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.process_name +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.model +'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.quantity +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ value.employee +'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.st_date +'</td>';
					tableData += '<td style="text-align:left;padding-left:4px;">'+ (value.status_material || '') +'</td>';
					tableData += '</tr>';
				});
				$('#example1Body').append(tableData);

				var table = $('#example1').DataTable({
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
				
				

				$('#example1 tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
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

				$('#example1 tfoot tr').appendTo('#example1 thead');

				$('#loading').hide();

			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection