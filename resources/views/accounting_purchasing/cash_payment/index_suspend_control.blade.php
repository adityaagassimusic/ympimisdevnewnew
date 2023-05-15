@extends('layouts.display')
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
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
	}
	.container {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.container input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.container .checkmark:after {
	  left: 10px;
	  top: 5px;
	  width: 5px;
	  height: 12px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Form {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
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
	<div class="col-md-12" style="padding:0">
		<div class="col-md-6" style="padding:2px">
			<div class="box">
				<div class="box-body">
					<div class="form-group row" style="background-color: #3f51b5;text-align: center;color: white;padding: 10px;margin:0px;margin-bottom: 10px !important;border-radius: 2px;">
						<label class="col-xs-12 header-tab" style="font-size:20px">List Outstanding Suspense</label>
					</div>

					<table id="listTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Date</th>
								<th>Title</th>
								<th>Amount</th>
								<th>Document</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="listTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12" style="padding:0">
		<div class="col-md-6" style="padding:2px">
			<div class="box">
				<div class="box-body">
					<div class="form-group row" style="background-color: #ff9800;text-align: center;color: white;padding: 10px;margin:0px;margin-bottom: 10px !important;border-radius: 2px;">
						<label class="col-xs-12 header-tab" style="font-size:20px" id="judul_suspend">List Detail Suspense</label>
					</div>

					<table id="detailTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Employee Name</th>
								<th>Amount</th>
								<!-- <th>Upload Nota</th> -->
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="detailTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;"></h4></center>
			</div>
			<div class="modal-body">
				<table class="table table-bordered" id="tableDetail">
					<thead>
						<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
							<th style="width: 1%">No.</th>
							<th style="width: 5%">No PR</th>
							<th style="width: 10%">Detail</th>
							<th style="width: 5%">Harga</th>
							<th style="width: 5%">Status</th>
						</tr>
					</thead>
					<tbody id="bodyTableDetail">
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

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


    var no = 0;

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


	$(function () {
		$('.select4').select2({
			allowClear:true,
			dropdownAutoWidth : true,
			tags: true,
	        dropdownParent: $('#modalNew')
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearNew(){
		$('#id_edit').val('');
		$('#title').val('');
		// $("#category").val('').trigger('change');
		$('#currency').val('').trigger('change');
		$("#amount").val('');
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/suspend/control") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";

				$.each(result.suspend, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:1%;">'+value.submission_date+'</td>';
					listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:3%;">'+value.title+'</td>';
					listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:2%;text-align:right">'+value.currency+' '+value.amount.toLocaleString()+'</td>';

					if (value.file != null) {
						listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;"><a target="_blank" href="{{ url("files/cash_payment/suspend") }}/'+value.file+'"><i class="fa fa-paperclip"></i></td>';
					}
					else{
						listTableBody += '<td onclick="detail(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button></center></td>';
					}

					else{
						listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
					}

					listTableBody += '</tr>';

				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
					'responsive':true,
					'paging': false,
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

	function detail(id){

		var data = {
			id:id
		}

		$.get('{{ url("fetch/suspend/control/detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#detailTable').DataTable().clear();
				$('#detailTable').DataTable().destroy();				
				$('#detailTableBody').html("");
				var detailTableBody = "";

				$('#judul_suspend').text("List Detail Suspense "+result.suspend[0].title);

				$.each(result.suspend, function(key, value){
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					detailTableBody += '<td style="width:2%;">'+value.emp_name+'</td>';
					detailTableBody += '<td style="width:2%;text-align:right">'+value.item_currency+' '+value.amount.toLocaleString()+'</td>';
					// detailTableBody += '<td style="width:1%;"><input type="file" id="file_attach" name="file_attach"></td>';
					if (value.received_at == null) {
						detailTableBody += '<td style="width:1%;"><button class="btn btn-md btn-warning" data-toggle="tooltip" title="Detail Suspend" style="margin-right:5px;" onclick="detail_suspend(\''+value.id+'\',\''+value.emp_name+'\')"><i class="fa fa-eye"></i></button> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Give Money To Employee" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\',\''+value.emp_name+'\',\''+value.emp_id+'\')"> <i class="fa fa-check-square-o"></i> <i class="fa fa-user"></i></button></td>';
					}else{

						detailTableBody += '<td style="width:1%;"><button class="btn btn-md btn-warning" data-toggle="tooltip" title="Detail Suspend" style="margin-right:5px;" onclick="detail_suspend(\''+value.id+'\',\''+value.emp_name+'\')"><i class="fa fa-eye"></i></button> <span class="label label-success">Sudah Diterima User</span></td>';
					}
					detailTableBody += '</tr>';
				});

				$('#detailTableBody').append(detailTableBody);

				$('#detailTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#detailTable').DataTable({
					'responsive':true,
					'paging': false,
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

	function detail_suspend(id, name) {
		$('#loading').show();
		var data = {
			id:id,
			name:name
		}

		$.get('{{ url("fetch/suspend/report/detail") }}',data, function(result, status, xhr){
			if(result.status){
				$('#myModalLabel').html("Detail Pembayaran "+name);

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				$('#bodyTableDetail').html("");

				var total_point = 0;
				var tableData = "";

				$.each(result.suspend, function(key, value) {
						tableData += '<tr>';
						tableData += '<td  style="width: 1%;border:1px solid black;padding:2px">'+ parseInt(key+1) +'</td>';
						tableData += '<td  style="width: 3%;;border:1px solid black;padding:2px">'+ value.no_pr +'</td>';
						tableData += '<td style="width: 10%;border:1px solid black;padding:2px">'+ value.detail +'</td>';
						tableData += '<td style="width: 5%;text-align:center;border:1px solid black;padding:2px">'+ value.item_currency +' '+ (value.amount/1000).toFixed(3) +'</td>';
						if (value.settle == null) {
							tableData += '<td style="width: 3%;border:1px solid black;padding:2px;background-color:red;color:white">Open</td>';
						}else{
							tableData += '<td style="width: 3%;border:1px solid black;padding:2px;background-color:green;color:white">Close</td>';
						}
						tableData += '</tr>';
						key++;
				});
				$("#bodyTableDetail").append(tableData);

				var table = $('#tableDetail').DataTable({
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

				$('#loading').hide();

				$('#modalDetail').modal('show');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!','Failed Get Data');
			}
		});
   }

   function sendEmail(id,name,emp_id) {
      var data = {
        id:id,
				name:name,
				emp_id:emp_id
      };

      if (!confirm("Apakah Anda Memberi Uang Cash Kepada " +name+ " ?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("give/suspend") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Uang Berhasil Diterima");
      	$("#loading").hide();
      	detail(id);
      })
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

