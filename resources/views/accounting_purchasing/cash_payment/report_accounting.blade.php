@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		/*text-align:center;*/
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<!-- <h1>
		Report Petty Cash ()<span class="text-purple">{{ $title_jp }}</span>
	</h1>  -->
	<ol class="breadcrumb">
		<li>

		</li>
		<?php if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") { ?>
		
		<?php } ?>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
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
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
						<div class="box-body">
							<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
								<div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;color:white" align="center">
									<span style="font-size: 25px;color: black;width: 25%;">SETTLEMENT FROM USERS</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-body" style="padding-top: 0;">
							<table id="toolTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Submission Date</th>
										<!-- <th style="width:5%;">No PR</th> -->
										<th style="width:15%;">Detail</th>
										<!-- <th style="width:5%;">Amount Suspense</th> -->
										<th style="width:5%;">Lampiran / Nota</th>
										<th style="width:5%;">Amount Settle</th>
									</tr>
								</thead>
								<tbody id="tableBodyResult">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!--<div class="row">
				<div class="col-xs-6">
					<div class="box no-border">
						<div class="box-body">
							<table id="suspensetable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:2%;">No PR</th>
										<th style="width:6%;">Detail</th>
										<th style="width:3%;">Amount Suspense</th>
										<th style="width:3%;">Amount Settle</th>
										<th style="width:3%;">Result</th>
										<th style="width:2%;">Nota</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
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

				<div class="col-xs-6">
					<div class="box no-border">
						<div class="box-body">
							<table id="pettycashtable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:2%;">No PR</th>
										<th style="width:6%;">Detail</th>
										<th style="width:3%;">Suspense</th>
										<th style="width:3%;">Settle</th>
										<th style="width:3%;">Result</th>
										<th style="width:2%;">Nota</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
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
				</div> -->
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		// fetchTable();
		fillTable();
		$('body').toggleClass("sidebar-collapse");

	});

	// $('#keyword2').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		fetchTable();
	// 	}
	// });

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function getFormattedDate(date) {
	    var year = date.getFullYear();

	    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
	      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
	    ];

	    var month = date.getMonth();

	    var day = date.getDate().toString();
	    day = day.length > 1 ? day : '0' + day;
	    
	    // return day + ' ' + monthNames[month] + ' ' + year;
	    return day + '-' +monthNames[month] + '-' + year;

	  }

	function fillTable(){
	    $('#loading').show();

	    $.get('{{ url("fetch/report/petty_cash/settlement") }}', function(result, status, xhr){

	      $('#toolTable').DataTable().clear();
	      $('#toolTable').DataTable().destroy();
	      $('#tableBodyResult').html("");

	      var tableData = "";

	      $.each(result.settlement, function(key, value) {
	       tableData += '<tr>';     
	       tableData += '<td>'+ getFormattedDate(new Date(value.submission_date)) +'</td>';
	       // tableData += '<td>'+ value.no_pr+'</td>';
	       tableData += '<td>'+ value.description+'</td>';
	       // tableData += '<td style="text-align:right">'+ parseInt(value.amount_suspend).toLocaleString('de-DE')+'</td>';
	       tableData += '<td style="text-align:center"><a href="{{ url("files/cash_payment/settlement") }}/'+value.nota+'" target="_blank" class="fa fa-paperclip"></a></td>';
	       tableData += '<td style="text-align:right">'+parseInt( value.amount_settle).toLocaleString('de-DE')+'</td>';
	       tableData += '</tr>';     
	     });


	      $('#tableBodyResult').append(tableData);

	     
	      var table = $('#toolTable').DataTable({
	        'dom': 'Bfrtip',
	        'responsive':true,
	        'lengthMenu': [
	        [ 5, 10, 25, -1 ],
	        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
	        'pageLength': 25,
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

	      $('#loading').hide();
	    })
	    
	}

	function fetchTable(){
		$('#pettycashtable').DataTable().destroy();

		var keyword = $('#keyword2').val();

		var data = {
			keyword:keyword
		}
		
		$('#pettycashtable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );

		var table = $('#pettycashtable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
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
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url('fetch/report/petty_cash') }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_pr"},
			{ "data": "detail"},
			{ "data": "amount_suspend"},
			{ "data": "amount_settle"},
			{ "data": "status"},
			{ "data": "image"}
			]
		});

		table.columns().every( function () {
			var that = this;

			$('#search', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );
		
		$('#pettycashtable tfoot tr').appendTo('#pettycashtable thead');
	}
</script>
@endsection

