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
		text-align: left;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: left;
	}

	table.table-bordered > tfoot > tr > th{
		text-align: right;
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
		<div class="box-header">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="col-md-2 col-md-offset-10" style="padding-right: 0;">
				<div class="form-group">
						<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create List Payment</a>
				</div>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				
			</div>
			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Vendor</th>
						<th>Date Payment</th>
						<th>Currency</th>
						<th>ID Payment</th>
						<!-- <th>Status</th> -->
						<th>Action</th>
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
						<!-- <th></th> -->
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog" style="width: 80%">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
				<div class="row">
					<input type="hidden" id="id_edit">
					<div class="col-md-12">

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_id" class="col-sm-3 control-label">ID Payment<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="payment_id" name="payment_id" data-placeholder='Choose ID Payment' style="width: 100%" onChange="changeCode(this.value)">
									<option value="">&nbsp;</option>
									@foreach($payment as $pay)
									<option value="{{$pay->acc_payment}}">{{$pay->acc_payment}}</option>
									@endforeach
								</select>
							</div>
						</div> 
						
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_due_date" class="col-sm-3 control-label">Due Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="payment_due_date" name="payment_due_date" data-placeholder='Due Date' style="width: 100%">
								</select>
							</div>
						</div> 

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="supplier_code" class="col-sm-3 control-label">Vendor Name<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="supplier_name" name="supplier_name" data-placeholder='Choose Supplier Name' style="width: 100%">
								</select>
							</div>
						</div>
						
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="Currency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="currency" name="currency" data-placeholder='Currency' style="width: 100%">
									<option value="">&nbsp;</option>
									<option value="USD">USD</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
									<option value="EUR">EUR</option>
								</select>
							</div>
						</div>
						
					</div>
				</div>
				<br>
				<div class="col-md-3">
					<a class="btn btn-success" onclick="preview()" style="width: 100%; font-size: 12px;" id="newButton"><i class="fa fa-eye"></i> Preview</a>
				</div>

				<div class="col-md-3 col-md-offset-6">
					<a class="btn btn-info pull-right" onclick="generate()" style="width: 100%; font-size: 12px;" id="newButton"><i class="fa fa-recycle"></i> Generate Report</a>
				</div>

				<div class="col-xs-12" style="margin-top: 3%;">
					<p style="font-size: 1.2vw;">List Surat Jalan</p>
                    <div class="box box-primary">
                        <div class="box-body">
                            <table class="table table-hover table-bordered table-striped" id="tableList">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 1%; text-align: center;">Invoice No</th>
                                        <th style="width: 2%; text-align: center;">Surat Jalan</th>
                                        <th style="width: 2%; text-align: center;">PO Number</th>
                                        <th style="width: 2%; text-align: center;">Detail Product</th>
                                        <th style="width: 2%; text-align: center;">Amount</th>
                                        <th style="width: 2%; text-align: center;">PPN</th>
                                        <th style="width: 2%; text-align: center;">PPH</th>
                                        <th style="width: 2%; text-align: center;">Net Payment</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyList">
                                </tbody>
                                <tfoot style="background-color: rgb(252, 248, 227);">
									<tr>
										<th colspan="4" style="text-align:center;">Total:</th>
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
</div>


<div class="modal fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="modaldeletehide()">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus payment request ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="modaldeletehide()">Cancel</button>
                <a id="a" name="modalButtonDelete" type="button"  onclick="delete_payment_request(this.id)" class="btn btn-danger" style="color:white">Delete</a>
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


	var payment = $.parseJSON('<?php echo $payment; ?>');

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

	$(function () {
		$('.select4').select2({
			allowClear:true,
			dropdownAutoWidth : true
		});

	})
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }

	function newData(id){	
		$('#modalNewTitle').text('Create List Payment');
		$('#newButton').show();
		$('#modalNew').modal('show');

		$("#payment_id").val("").trigger('change');
		$("#supplier_name").val("").trigger('change');
		$("#payment_due_date").val("").trigger('change');
		$("#currency").val("").trigger('change');
	}

	function fetchTable(){
		$('#loading').show();

		$.get('{{ url("fetch/billing/list_payment") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.payment, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:3%;">'+value.supplier_name+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.payment_due_date))+'</td>';
					listTableBody += '<td style="width:1%;">'+value.currency+'</td>';
					listTableBody += '<td style="width:2%;">'+value.payment_id+'</td>';
					// listTableBody += '<td style="width:1%;">'+(value.status)+'</td>';
					listTableBody += '<td style="width:2%;text-align:center"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_list") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> Report</a></center></td>';
				
					listTableBody += '</tr>';


					count_all += 1;
				});

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
					"processing": true,
					initComplete: function() {
                    this.api()
                        .columns([1,2])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tableFinished th").eq([dd]).text();
                            var select = $(
                                    '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tableFinished th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                	},
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


	function changeCode(value) {

		$("#payment_due_date").html('');
		$("#supplier_name").html('');

		var due_date = '';
		due_date += '<option value=""></option>';

		var supplier = '';
		supplier += '<option value=""></option>';

		for(var i = 0; i < payment.length;i++){
			if (value == payment[i].acc_payment ) {
				due_date += '<option value="'+payment[i].payment_due_date+'">'+payment[i].payment_due_date+'</option>';
				supplier += '<option value="'+payment[i].supplier_name+'">'+payment[i].supplier_name+'</option>';
			}else{
				due_date += '<option value=""></option>';
				supplier += '<option value=""></option>';
			}
		}
		$("#payment_due_date").append(due_date);
		$("#supplier_name").append(supplier);
	}


	function preview(){

			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";

			var id_payment = $("#payment_id").val();
			var due_date = $("#payment_due_date").val();
			var supplier_name = $("#supplier_name").val();
			var currency = $("#currency").val();
			
            for (var i = 0; i < payment.length; i++) {
				if(payment[i].acc_payment == id_payment && payment[i].payment_due_date == due_date && payment[i].supplier_name == supplier_name && payment[i].currency == currency){

                    tableData += '<tr>';

                    tableData += '<td style="text-align: center;">';
                    tableData += payment[i].invoice;
                    tableData += '</td>';

                    tableData += '<td style="text-align: left;">';
                    tableData += payment[i].surat_jalan;
                    tableData += '</td>';

                    tableData += '<td style="text-align: center;">';
                    tableData += payment[i].po_number;
                    tableData += '</td>';

                    tableData += '<td style="text-align: center;">';
                    tableData += '';
                    tableData += '</td>';

                    tableData += '<td style="text-align: right;">';
                    tableData += parseFloat(payment[i].amount).toFixed(2);
                    tableData += '</td>';

                    tableData += '<td style="text-align: right;">';
                    if (payment[i].ppn != null) {
						tableData += parseFloat(payment[i].ppn*payment[i].amount/100).toFixed(2);
                    }else{
                    	tableData += 0;	
                    }
                    
                    tableData += '</td>';


                    tableData += '<td style="text-align: right;">';
                    if (payment[i].pph != null) {
                    	tableData += parseFloat(payment[i].pph*payment[i].amount/100).toFixed(2);
                    }else{
                    	tableData += 0;
                    }
                    // tableData += payment[i].pph;
                    tableData += '</td>';

                    tableData += '<td style="text-align: right;">';
                    tableData += parseFloat(payment[i].net_payment).toFixed(2);
                    tableData += '</td>';

                    tableData += '</tr>';
		        
		        }
            }
			$('#tableBodyList').append(tableData);


			$('#tableList').DataTable({
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
				"footerCallback": function (tfoot, data, start, end, display) {
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$%,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					var api = this.api();
					var total_amount = api.column(4).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)

					var total_ppn = api.column(5).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)

					var total_pph = api.column(6).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)

					var total_net = api.column(7).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)

					$(api.column(4).footer()).html(total_amount.toLocaleString());
					$(api.column(5).footer()).html(total_ppn.toLocaleString());
					$(api.column(6).footer()).html(total_pph.toLocaleString());
					$(api.column(7).footer()).html(total_net.toLocaleString());
				},
				'paging': true,
				'lengthChange': true,
				'pageLength': 10,
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
	}

	function generate(){	
		$('#loading').show();

		if($("#payment_id").val() == "" || $('#supplier_name').val() == "" || $('#payment_due_date').val() == "" || $('#currency').val() == ""){
			$('#loading').hide();
			openErrorGritter('Error', "Please fill field with (*) sign.");
			return false;
		}

		var formData = new FormData();
		formData.append('payment_id', $("#payment_id").val());
		formData.append('supplier_name', $("#supplier_name").val());
		formData.append('payment_due_date', $("#payment_due_date").val());
		formData.append('currency', $("#currency").val());

		$.ajax({
			url:"{{ url('billing/create/list_payment') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success', data.message);
					audio_ok.play();
					$('#modalNew').modal('hide');
					$('#loading').hide();
					fetchTable();
				}else{
					openErrorGritter('Error!',data.message);
					$('#loading').hide();
					audio_error.play();
				}

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

