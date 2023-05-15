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
			<form method="GET" action="{{ url("export/payment_request") }}">
				<div class="col-md-2" style="padding: 0">
					<div class="form-group">
						<label>Date From</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom">
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>Date To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="col-md-4" style="padding-right: 0;">
							<label style="color: white;"> x</label>
							<button type="submit" class="btn btn-primary form-control"><i class="fa fa-download"></i> Export Payment Request</button>
						</div>
					</div>
				</div>
				<div class="col-md-2" style="padding-right: 0;">
					<div class="form-group">
							<label style="color: white;"> x</label>
							<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Payment Request</a>
					</div>
				</div>
			</form>
		</div>
		<div class="box-body">
			<div class="row">
				
			</div>

			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Submission Date</th>
						<th>Vendor</th>
						<th>Payment Term</th>
						<th>Due Date</th>
						<th>Kind Of Material</th>
						<th>Amount</th>
						<!-- <th>Document Attach</th> -->
						<th>Status</th>
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
						<th></th>
						<th></th>
						<!-- <th></th> -->
						<th></th>
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
					<div class="col-md-6">

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_date" class="col-sm-3 control-label">Submission Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>

	                                <input type="text" class="form-control" value="<?= date('d-M-Y') ?>" disabled="">
	                                <input type="text" class="form-control" value="{{ date('Y-m-d') }}" id="payment_date" name="payment_date" style="display: none;">
								</div>
							</div>
						</div>
						
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="supplier_code" class="col-sm-3 control-label">Vendor Name<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="supplier_code" name="supplier_code" data-placeholder='Choose Supplier Name' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($vendor as $ven)
									<option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
									@endforeach
								</select>
								<input type="hidden" class="form-control" id="supplier_name" name="supplier_name" readonly="">
							</div>
						</div>
						
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="Currency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="currency" name="currency" data-placeholder='Currency' style="width: 100%" onchange="getSupplier(this)">
									<option value="">&nbsp;</option>
									<option value="USD">USD</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
									<option value="EUR">EUR</option>
								</select>
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_term" class="col-sm-3 control-label">Payment Term<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="payment_term" name="payment_term" data-placeholder='Choose Payment Term' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($payment_term as $pt)
									<option value="{{$pt->payment_term}}">{{$pt->payment_term}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_due_date" class="col-sm-3 control-label">Due Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="payment_due_date" name="payment_due_date" placeholder="Due Date" readonly>
								</div>
							</div>
						</div> 
						
					</div>
					<div class="col-md-6">
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="amount" class="col-sm-3 control-label">Amount<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="amount" name="amount" placeholder="Total Amount" readonly="" style="text-align: right;">
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="material" class="col-sm-3 control-label">Kind Of Material<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="kind_of" name="kind_of" data-placeholder='Kind Of Material' style="width: 100%">
									<option value="">&nbsp;</option>
									<option value="Raw Materials">Raw Materials</option>
									<option value="Other (PPH)">Other (PPH)</option>
									<option value="Constool">Constool</option>
									<option value="Canteen">Canteen</option>
								</select>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="document" class="col-sm-3 control-label">Attached Documents</label>
							<div class="col-sm-9">
							    <label class="container">
							  		Local
								 	<input type="checkbox" id="doc_local" name="check_payment" class="check_payment" value="local">
								 	<span class="checkmark"></span>
								</label>
								<label class="container">
								 	Import
								    <input type="checkbox" id="doc_import" name="check_payment" class="check_payment" value="import">
								 	<span class="checkmark"></span>
								</label>
							    <label class="container">
							  		Invoice
								 	<input type="checkbox" id="doc_invoice" name="check_payment" class="check_payment" value="invoice">
								 	<span class="checkmark"></span>
								</label>
								<label class="container">
							  		Surat Jalan
								 	<input type="checkbox" id="doc_surat_jalan" name="check_payment" class="check_payment" value="surat_jalan">
								 	<span class="checkmark"></span>
								</label>
								<label class="container">
							  		Purchase Order
								 	<input type="checkbox" id="doc_purchase_order" name="check_payment" class="check_payment" value="purchase_order">
								 	<span class="checkmark"></span>
								</label>
								<label class="container">
							  		Faktur Pajak
								 	<input type="checkbox" id="doc_faktur_pajak" name="check_payment" class="check_payment" value="faktur_pajak">
								 	<span class="checkmark"></span>
								</label>
								<label class="container">
							  		Receipt
								 	<input type="checkbox" id="doc_receipt" name="check_payment" class="check_payment" value="receipt">
								 	<span class="checkmark"></span>
								</label>

							</div>
						</div>
						<!-- <div class="col-md-12" style="margin-bottom: 5px">
							<label for="file" class="col-sm-3 control-label">File</label>
							<div class="col-sm-9">
								<input type="file" id="file_attach" name="file_attach">
							</div>
						</div> -->
					</div>
				</div>
				<div class="col-md-12" style="padding:0" style="">
                    <div class="row detail_create" style="padding:20px">
                         <div class="col-md-1" style="padding:5px;">
                            <b>No</b>
                        </div>
                        <div class="col-md-2" style="padding:5px;">
                            <b>No Invoice</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Amt (DPP)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>PPN (%)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Jenis PPH</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Amt (Jasa)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>PPH (%)</b>
                        </div>
                        <div class="col-md-2" style="padding:5px;">
                            <b>Net Payment</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Invoice</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Aksi</b>
                        </div>

                        <input type="text" name="lop" id="lop" value="1" hidden>

                        <div class="col-md-1" style="padding:5px;">
                            1
                        </div>

                        <div class="col-md-2" style="padding:5px;">
                            <select class="form-control select2" data-placeholder="Choose Invoice" name="invoice1" id="invoice1" style="width: 100% height: 35px;" onchange="pilihInvoice(this)" required="">
                            </select>
                        </div>
                        
                        <div class="col-md-1" style="padding:5px;">
                            <input type="hidden" class="form-control" id="invoice_number1" name="invoice_number1" required="">
                            <input type="text" class="form-control" id="amount1" name="amount1" required="" style="text-align:right">
                        </div>

                        <div class="col-md-1" style="padding:5px;">
                            <!-- <input type="text" class="form-control" id="amountppn1" name="amountppn1"> -->
                            	<!-- <input type="hidden" class="form-control" id="amountppn1" name="amountppn1"> -->
                            <label class="container">
							 	<input type="checkbox" id="ppncheck1" name="ppncheck1" onclick="cekPPN(this)">
							 	<span class="checkmark"></span>

                            	<input type="text" class="form-control" id="ppn1" name="ppn1" onkeyup="withPPN(this)" readonly="" style="height: 34px;width: 50%;opacity: 1;" readonly>
							</label>							


                        </div>

                        <div class="col-md-1" style="padding:5px;">
                            <!-- <input type="text" class="form-control" id="type_pph1" name="type_pph1" required=""> -->
                            <select class="form-control select2" data-placeholder="Choose Type PPH" name="typepph1" id="typepph1" style="width: 100% height: 35px;" onchange="pilihPPH(this)" required="">
                                <option value=""></option>
                                <option value="all">All</option>
                                <option value="partial">Partial</option>
                                <option value="none">None</option>
                            </select>
                        </div>


                        <div class="col-md-1" style="padding:5px;">
                            <input type="text" class="form-control" id="amount_jasa1" name="amount_jasa1" required="" style="text-align:right">
                        </div>

                        <div class="col-md-1" style="padding:5px;">
                            <input type="text" class="form-control" id="pph1" name="pph1" onkeyup="getTotal(this.id)">
                        </div>

                        <div class="col-md-2" style="padding:5px;">
                            <input type="text" class="form-control" id="amount_final1" name="amount_final1" required="" style="text-align:right">
                        </div>

                        <div class="col-md-1" style="padding:5px;">
                            <span id="file1" name="file1" class="file1"></span>
                        </div>

                        <div class="col-md-1" style="padding:5px;">
                            <a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus'></i></a>
                        </div>
                    
                        <div id="tambah"></div>
                    </div>

                     <div class="row detail_edit" style="padding:20px">
                     	<div class="col-md-1" style="padding:5px;">
                             <b>No</b>
                        </div>
                        <div class="col-md-2" style="padding:5px;">
                            <b>No Invoice</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Amt (DPP)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>PPN (%)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Jenis PPH</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Amount (Jasa)</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>PPH (%)</b>
                        </div>
                        <div class="col-md-2" style="padding:5px;">
                            <b>Net Payment</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>File</b>
                        </div>
                        <div class="col-md-1" style="padding:5px;">
                            <b>Aksi</b>
                        </div>
                    </div>
                    <div class="row detail_edit" id="modalDetailBodyEdit" style="padding:20px">
                    </div>

                    <div id="tambah2" style="padding-left:20px;padding-right: 20px;">
                        <input type="text" name="lop2" id="lop2" value="1" hidden="">
                        <input type="text" name="looping" id="looping" hidden="">
                    </div>
                        
                </div>
					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
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


<div class="modal fade" id="modalPR" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        	<h4 class="modal-title" id="myModalLabel">Konfirmasi</h4>
      		</div>
	      	<div class="modal-body">
		        <div class="box-body">
		            <input type="hidden" value="{{csrf_token()}}" name="_token" />
		            <div class="row">
			          <div class="col-xs-12">
			            Apakah anda yakin ingin menyerahkan dokumen ke departemen accounting?
			           </div>
		          	</div>
		        </div>
	     	</div>
		    <div class="modal-footer">
		      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
		      <input type="hidden" id="id_edit_pr">
		      <button type="button" onclick="confirm_pr()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Confirm</button>
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
	var no = 2;
	var amount_total = 0;
    invoice_list = "";
	var payment_term_all = null;

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
			dropdownAutoWidth : true,
	        dropdownParent: $('#modalNew')
		});
	})

	// $("#amount").change(function(){
	// 	var output = parseFloat($('#amount').val()); 
	// 	var output2 = output.toLocaleString(undefined,{'minimumFractionDigits':2,'maximumFractionDigits':2});
	// 	$('#amount').val(output2);
 //  	});

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
		if(id == 'new'){
			$('#modalNewTitle').text('Create Payment Request');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');

            $('.detail_create').show();
            $('.detail_edit').hide();
		}
		else{
			$('#newButton').hide();
			$('#updateButton').show();
            $('.detail_create').hide();
            $('.detail_edit').show()

			var data = {
				id:id
			}
			$.get('{{ url("detail/billing/payment_request") }}', data, function(result, status, xhr){
				if(result.status){

					$('#supplier_code').html('');
					$('#payment_term').html('');
					$('#currency').html('');
					$('#kind_of').html('');

					var supplier_code = "";
					var payment_term = "";
					var currency = "";
					var kind_of = "";

					$('#payment_date').val(result.payment.payment_date);

					$.each(result.vendor, function(key, value){
						if(value.vendor_code == result.payment.supplier_code){
							supplier_code += '<option value="'+value.vendor_code+'" selected>'+value.vendor_code+' - '+value.supplier_name+'</option>';
						}
						else{
							supplier_code += '<option value="'+value.vendor_code+'">'+value.vendor_code+' - '+value.supplier_name+'</option>';
						}
					});

					$('#supplier_code').append(supplier_code);
					$('#supplier_name').val(result.payment.supplier_name);

					if(result.payment.currency == "USD"){
						currency += '<option value="USD" selected>USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.payment.currency == "IDR"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR" selected>IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.payment.currency == "JPY"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY" selected>JPY</option>';
					}

					$('#currency').append(currency);

					$.each(result.payment_term, function(key, value){
						if(value.payment_term == result.payment.payment_term){
							payment_term += '<option value="'+value.payment_term+'" selected>'+value.payment_term+'</option>';
						}
						else{
							payment_term += '<option value="'+value.payment_term+'">'+value.payment_term+'</option>';
						}
					});

					$('#payment_term').append(payment_term);

					$('#payment_due_date').val(result.payment.payment_due_date);
					$('#amount').val(result.payment.amount);

					if (result.payment.kind_of == "Raw Materials"){
						kind_of += '<option value="Raw Materials" selected>Raw Materials</option>';
						kind_of += '<option value="Other (PPH)">Other (PPH)</option>';
						kind_of += '<option value="Constool">Constool</option>';
					}
					else if (result.payment.kind_of == "Other (PPH)"){
						kind_of += '<option value="Raw Materials">Raw Materials</option>';
						kind_of += '<option value="Other (PPH)" selected>Other (PPH)</option>';
						kind_of += '<option value="Constool">Constool</option>';
					}
					else if (result.payment.kind_of == "Constool"){
						kind_of += '<option value="Raw Materials">Raw Materials</option>';
						kind_of += '<option value="Other (PPH)">Other (PPH)</option>';
						kind_of += '<option value="Constool" selected>Constool</option>';
					}

					$('#kind_of').append(kind_of);

					var attach = [];
                    var type = [];

                    attach = result.payment.attach_document.split(",");

                    $("input[name='check_payment']").each(function (i) {
                        type[i] = $(this).val();
                        $('.check_payment')[i].checked = false;
                    });
                    for (var i  = 0;i < attach.length; i++) {
                        for (var j  = 0;j < type.length; j++) {
                            if(type[j] == attach[i]){
                                $('.check_payment')[j].checked = true;
                            }
                        }
                    }

                    var ids = [];
                    $('#modalDetailBodyEdit').html('');

                    $.each(result.payment_detail, function(key, value) {
                        var tambah2 = "tambah2";
                        var lop2 = "lop2";

                        isi = "<div class='col-md-1' style='padding:5px;'>"+value.id+"</div>";
                        isi += "<div class='col-md-2' style='padding:5px;'>  <input type='text' class='form-control' id='invoice"+value.id+"'' name='invoice"+value.id+"' value='"+value.id_invoice+"' readonly=''> </div>"; 
                        isi += "<div class='col-md-1' style='padding:5px;'> <input type='hidden' class='form-control' id='invoice_number"+value.id+"'' name='invoice_number"+value.id+"' value='"+value.invoice+"' readonly=''> <input type='text' class='form-control' id='amount"+value.id+"'' name='amount"+value.id+"'' required=''  value='"+value.amount+"' style='text-align:right' readonly></div>";
                        // isi += "<div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='ppn"+value.id+"'' name='ppn"+value.id+"'' value='"+value.ppn+"' readonly></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'><input type='text' class='form-control' name='ppn"+value.id+"' id='ppn"+value.id+"' value='"+(value.ppn || '')+"' readonly=''></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'><input type='text' class='form-control' name='typepph"+value.id+"' id='typepph"+value.id+"' value='"+(value.typepph || '')+"' readonly=''></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='amount_jasa"+value.id+"' name='amount_jasa"+value.id+"' required='' style='text-align:right' value='"+(value.amount_service|| '')+"' readonly=''> </div>";
                        isi += "<div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='pph"+value.id+"' name='pph"+value.id+"' value='"+(value.pph || '')+"' readonly=''> </div>";
                        isi += "<div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_final"+value.id+"' name='amount_final"+value.id+"' style='text-align:right' required='' value='"+(value.net_payment|| '')+"' readonly=''> </div>";
                        isi += "<div class='col-md-1' style='padding:5px;'> <span class='form-control'> <a href='{{url('files/invoice')}}/"+value.file+"' target='_blank' class='fa fa-paperclip'> Check</a></span></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'><a href='javascript:void(0);' id='"+ value.id +"' onclick='deleteConfirmation(\""+ value.item_desc +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button></div>";

                        // isi += "<div class='col-md-1' style='padding:5px;'></div>";

                        ids.push(value.id);

                        $('#modalDetailBodyEdit').append(isi);

                        $(function () {
                            $('.select5').select2({
                                dropdownAutoWidth : true,
                                allowClear: true
                            });
                        })

                        $("#looping").val(ids);

                    });

					$('#id_edit').val(result.payment.id);
					$('#modalNewTitle').text('Update Payment Request');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

	function Save(id){	
		$('#loading').show();

		if(id == 'new'){
			if($("#payment_date").val() == "" || $('#supplier_code').val() == "" || $('#currency').val() == "" || $('#payment_term').val() == "" || $('#payment_due_date').val() == "" || $('#amount').val() == "" || $('#kind_of').val() == ""){

				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var checked_payment = "";
			var ck = [];

            $.each($(".check_payment"), function(key, value) {
            	if($(this).is(":checked")){
	                ck.push(value.value);
	            }
            });


            for (var i = 1; i < no; i++) {
            	if($("#typepph"+i).val() == "" || $('#invoice'+i).val() == "" || $('#amount_final'+i).val() == ""){
					$('#loading').hide();
					openErrorGritter('Error', "Please fill Invoice and PPH Type.");
					return false;
				}
            }

            checked_payment = ck.toString();

			var formData = new FormData();

			formData.append('category', "{{$category}}");
			formData.append('payment_date', $("#payment_date").val());
			formData.append('supplier_code', $("#supplier_code").val());
			formData.append('supplier_name', $("#supplier_name").val());
			formData.append('currency', $("#currency").val());
			formData.append('payment_term', $("#payment_term").val());
			formData.append('payment_due_date', $("#payment_due_date").val());
			formData.append('amount', $("#amount").val());
			formData.append('kind_of', $("#kind_of").val());
			formData.append('attach_document', checked_payment);
			// formData.append('file_attach', $('#file_attach').prop('files')[0]);
			formData.append('jumlah', no);
            
            for (var i = 1; i < no; i++) {
                formData.append('invoice'+i, $("#invoice"+i).val());
                formData.append('invoice_number'+i, $("#invoice_number"+i).val());
                formData.append('amount'+i, $("#amount"+i).val());
                formData.append('ppn'+i, $("#ppn"+i).val());
                formData.append('typepph'+i, $("#typepph"+i).val());
                formData.append('amount_service'+i, $("#amount_jasa"+i).val());
                formData.append('pph'+i, $("#pph"+i).val());
                formData.append('amount_final'+i, $("#amount_final"+i).val());
            }

			$.ajax({
				url:"{{ url('billing/create/payment_request') }}",
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
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#payment_date").val() == "" || $('#supplier_code').val() == null || $('#currency').val() == "" || $('#payment_term').val() == "" || $('#payment_due_date').val() == "" || $('#amount').val() == "" || $('#kind_of').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}


			var checked_payment = "";
			var ck = [];

			$.each($(".check_payment"), function(key, value) {
            	if($(this).is(":checked")){
	                ck.push(value.value);
	            }
            });

            checked_payment = ck.toString();

			var formData = new FormData();
			formData.append('id_edit', $("#id_edit").val());
			formData.append('payment_date', $("#payment_date").val());
			formData.append('supplier_code', $("#supplier_code").val());
			formData.append('supplier_name', $("#supplier_name").val());
			formData.append('currency', $("#currency").val());
			formData.append('payment_term', $("#payment_term").val());
			formData.append('payment_due_date', $("#payment_due_date").val());
			formData.append('amount', $("#amount").val());
			formData.append('attach_document', checked_payment);
			formData.append('kind_of', $("#kind_of").val());
			
			// formData.append('file_attach', $("#file_attach").prop('files')[0]);

			$.ajax({
				url:"{{ url('edit/payment_request') }}",
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
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
	}

	function clearNew(){
		no = 2;
		amount_total = 0;
	    invoice_list = "";
		$('#id_edit').val('');
		// $("#payment_date").val('');
		$("#supplier_code").val('').trigger('change');
		$("#supplier_name").val('');
		$('#currency').val('').trigger('change');
		$("#payment_term").val('').trigger('change');
		$("#payment_due_date").val('').trigger('change');
		$('#amount').val('');
		$("#kind_of").val('').trigger('change');
		$('input[type=checkbox]').prop('checked',false);
		$('#invoice1').val('').trigger('change');
		$('#invoice_number1').val('');
		$('#amount1').val('');
		$('#typepph1').val('').trigger('change');
		$('#amount_jasa1').val('');
		$('#pph1').val('');
		$('#amount_final1').val('');
		$('#file1').html('');
		$('#tambah').html('');
	}

	function fetchTable(){
		$('#loading').show();

		var category = "{{$category}}";

		var data = {
			category:category
		}

		$.get('{{ url("fetch/billing/payment_request") }}', data, function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				payment_term_all = result.payment_term;

				$.each(result.payment, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.payment_date))+'</td>';
					listTableBody += '<td style="width:3%;">'+value.supplier_code+' - '+value.supplier_name+'</td>';
					listTableBody += '<td style="width:3%;">'+value.payment_term+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.payment_due_date))+'</td>';
					listTableBody += '<td style="width:1%;">'+value.kind_of+'</td>';
					listTableBody += '<td style="width:1%;text-align:right">'+value.currency+' '+value.amount.toLocaleString()+'</td>';

					if (value.posisi == 'user') {
						listTableBody += '<td style="width:1%;background-color:#dd4b39">Not Sent</td>';
					}
					else if (value.posisi == 'manager'){
						listTableBody += '<td style="width:1%;background-color:#f39c12">Approval Manager</td>';
					}
					else if (value.posisi == 'dgm'){
						listTableBody += '<td style="width:1%;background-color:#f39c12">Approval DGM</td>';
					}
					else if (value.posisi == 'gm'){
						listTableBody += '<td style="width:1%;background-color:#f39c12">Approval GM</td>';
					}
					else if (value.posisi == 'acc_verif'){
						listTableBody += '<td style="width:1%;background-color:#00c0ef">Diverifikasi Accounting</td>';
					}
					else if (value.posisi == 'acc'){
						listTableBody += '<td style="width:1%;background-color:#00a65a">Diterima Accounting</td>';
					}
					else{
						listTableBody += '<td style="width:1%;background-color:#00a65a">Diterima Accounting</td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:4%;text-align:center"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-warning" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button><a href="javascript:void(0)" class="btn btn-md btn-danger" onClick="delete_payment('+value.id+')" style="margin-right:5px;color:white" title="Delete Payment Request"><i class="fa fa-trash"></i></a></center></td>';
					}
					else{
						if (value.posisi == "acc") {
							if (value.status_document == null) {
								listTableBody += '<td style="width:2%;text-align:center"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <a href="javascript:void(0)" data-toggle="modal" class="btn btn-md btn-warning" target="_blank" onClick="editPR('+value.id+')"><i class=" fa fa-hand-paper-o"></i> Hand Over</a></center></td>';
							}else{
								listTableBody += '<td style="width:2%;text-align:center"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <span style="color:green"> Document Sent</span></center></td>';
							}
						}else{
							if (value.status_document == null) {
								listTableBody += '<td style="width:2%;text-align:center"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-primary" data-toggle="tooltip" title="Resend Email" style="margin-right:5px;"  onclick="ResendEmail('+value.id+')"><i class="fa fa-envelope"></i> Resend</button><br><a href="javascript:void(0)" data-toggle="modal" class="btn btn-md btn-warning" target="_blank" onClick="editPR('+value.id+')"><i class=" fa fa-hand-paper-o"></i> Hand Over</a></center></td>';
							}else{
								listTableBody += '<td style="width:2%;text-align:center"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-primary" data-toggle="tooltip" title="Resend Email" style="margin-right:5px;"  onclick="ResendEmail('+value.id+')"><i class="fa fa-envelope"></i> Resend</button><br><span style="color:green"> Document Sent</span></center></td>';
							}
						}

						
						
					}

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
                        .columns([1,2,4,7])
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


		function tambah(id,lop) {
        var id = id;

        var lop = "";

        if (id == "tambah"){
            lop = "lop";
        }else{
            lop = "lop2";
        }

        var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px;'><div class='row'><div class='col-md-1' style='padding:5px;'>"+no+"</div><div class='col-md-2' style='padding:5px;'> <select class='form-control select3' data-placeholder='Choose Invoice' name='invoice"+no+"' id='invoice"+no+"' style='width: 100% height: 35px;' onchange='pilihInvoice(this)'> </select> </div><div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' required='' style='text-align:right'><input type='hidden' class='form-control' id='invoice_number"+no+"' name='invoice_number"+no+"' required=''></div><div class='col-md-1' style='padding:5px;'><label class='container'><input type='checkbox' id='ppncheck"+no+"' name='ppncheck"+no+"' onclick='cekPPN(this)'><span class='checkmark'></span><input type='text' class='form-control' id='ppn"+no+"' name='ppn"+no+"' onclick='withPPN(this)' readonly='' style='height: 34px;width: 50%;'></label></div><div class='col-md-1' style='padding:5px;'> <select class='form-control select3' data-placeholder='Choose Type PPH' name='typepph"+no+"' id='typepph"+no+"' style='width: 100% height: 35px;' onchange='pilihPPH(this)' required=''> <option value=''></option><option value='all'>All</option> <option value='partial'>Partial</option> <option value='none'>None</option> </select> </div><div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='amount_jasa"+no+"' name='amount_jasa"+no+"' required='' style='text-align:right'> </div><div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='pph"+no+"' name='pph"+no+"' onkeyup='getTotal(this.id)'> </div><div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_final"+no+"' name='amount_final"+no+"' required='' style='text-align:right'> </div><div class='col-md-1' style='padding:5px;'> <span id='file"+no+"' name='file"+no+"' class='file"+no+"'></div><div class='col-md-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div></div>");


        $("#"+id).append(divdata);
        $("#invoice"+no).append(invoice_list);

        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight:true
        });

        $(function () {
            $('.select3').select2({
                dropdownAutoWidth : true,
                dropdownParent: $("#"+id),
                allowClear:true,
                tags:true
                // minimumInputLength: 3
            });
        })

        // $("#"+id).select2().trigger('change');
        document.getElementById(lop).value = no;
        no+=1;
    }

    function pilihInvoice(elem)
    {
        var no = elem.id.match(/\d/g);
        no = no.join("");

        $.ajax({
            url: "{{ url('fetch/billing/payment_request/detail') }}?invoice="+elem.value,
            method: 'GET',
            success: function(data) {
                var json = data,
                obj = JSON.parse(json);
                $('#invoice_number'+no).val(obj.invoice).attr('readonly', true);
                $('#amount'+no).val(obj.amount).attr('readonly', true);
                $('#file'+no).html('<a href="{{url('files/invoice')}}/'+obj.file+'" target="_blank" class="fa fa-paperclip"> Check</a>');
		    	$('#ppn'+no).val("0").attr('readonly', false);
                $('#payment_due_date').val(obj.due_date).attr('readonly', true);
                // var amount_ppn = 0;
                
                // if (obj.ppn == "on") {
                //     amount_ppn = 0.1*parseInt(obj.amount);
                //     $('#amountppn'+no).val(amount_ppn).attr('readonly', true);
                // }else{
                //     amount_ppn = 0;
                // }
                // $('#ppn'+no).val(obj.ppn).attr('readonly', true);
            }
        });

        // alert(sel.value);
    }

    function pilihPPH(elem){
        var num = elem.id.match(/\d/g);
        num = num.join("");
        
        var amount_total = 0;
        var isi = elem.value;

        var amount = $('#amount'+num).val();
        var ppn = $('#ppn'+num).val();

        if (isi == "all") {
            $("#amount_jasa"+num).val(amount).attr('readonly',true);
            $("#pph"+num).val('').attr('readonly',false);
            $("#amount_final"+num).val('').attr('readonly',false);
        }
        else if(isi == "partial"){
            $("#amount_jasa"+num).val('').attr('readonly',false);
            $("#pph"+num).val('').attr('readonly',false);
            $("#amount_final"+num).val('').attr('readonly',false);
        }
        else{
            $("#amount_jasa"+num).val('').attr('readonly',true);
            $("#pph"+num).val('').attr('readonly',true);

            var amount = $('#amount'+num).val();
            var ppn = $('#ppn'+num).val();

            var amount_final = 0;
            
             if (ppn != null) {
                amount_final = parseFloat(amount) + parseFloat(amount*ppn/100)
                $("#amount_final"+num).val(amount_final).attr('readonly',true);
            }else{
                amount_final = parseFloat(amount);
                $("#amount_final"+num).val(amount_final).attr('readonly',true);
            }
        }
        

        for (var i = 1; i < no; i++) {
            amount_total += parseFloat($("#amount_final"+i).val());
        }
         $("#amount").val(amount_total).attr('readonly',true);

    }

    function getTotal(elem){
        var num = elem.match(/\d/g);
        num = num.join("");
        var isi = elem.value;

        var amount_total = 0;
        
        for (var i = 1; i < no; i++) {

            var amount = $('#amount'+i).val();
            var ppn = $('#ppn'+i).val();
            var amount_jasa = $('#amount_jasa'+i).val();
            var pph = $('#pph'+i).val();

            var amount_final = 0;

            var typepph = $('#typepph'+i).val();

            if (typepph == "all") {
                if (ppn != null) {
                    amount_final = parseFloat(amount) + parseFloat(amount*ppn/100) - (parseFloat(pph) * parseFloat(amount)/100);
                    amount_total += amount_final;
                }
                else{
                    amount_final = parseFloat(amount) - (parseFloat(pph) * parseFloat(amount)/100);
                    amount_total += amount_final;
                }

                $("#amount_final"+i).val(amount_final).attr('readonly',true);
            }
            else if(typepph == "partial"){
            	if (ppn != null) {
                    amount_final = parseFloat(amount) + parseFloat(amount*ppn/100) - (parseFloat(pph) * parseFloat(amount_jasa)/100);
                    amount_total += amount_final;
                }
                else{
                    amount_final = parseFloat(amount) - (parseFloat(pph) * parseFloat(amount_jasa)/100);
                    amount_total += amount_final;
                }

                $("#amount_final"+i).val(amount_final).attr('readonly',true);
            }
            else{
                if (ppn != null) {
                    amount_final = parseFloat(amount) + parseFloat(amount*ppn/100)
                    amount_total += amount_final;
                }else{
                    amount_final = parseFloat(amount);
                    amount_total += amount_final;
                }
            }
        }

        $("#amount").val(amount_total).attr('readonly',true);
    }

    function kurang(elem,lop) {

        var lop = lop;
        var ids = $(elem).parent('div').parent('div').parent('div').attr('id');
        var oldid = ids;
        $(elem).parent('div').parent('div').parent('div').remove();
        var newid = parseInt(ids) + 1;

        $("#"+newid).attr("id",oldid);
        $("#invoice"+newid).attr("name","invoice"+oldid);
        $("#invoice_number"+newid).attr("name","invoice_number"+oldid);
        $("#amount"+newid).attr("name","amount"+oldid);
        $("#amountppn"+newid).attr("name","amountppn"+oldid);
        $("#ppn"+newid).attr("name","ppn"+oldid);
        $("#pph"+newid).attr("name","pph"+oldid);
        $("#typepph"+newid).attr("name","typepph"+oldid);
        $("#amount_jasa"+newid).attr("name","amount_jasa"+oldid);
        $("#amount_final"+newid).attr("name","amount_final"+oldid); 
        $("#file"+newid).attr("name","file"+oldid);  

        $("#invoice"+newid).attr("id","invoice"+oldid);
        $("#invoice_number"+newid).attr("id","invoice_number"+oldid);
        $("#amount"+newid).attr("id","amount"+oldid);
        $("#amountppn"+newid).attr("id","amountppn"+oldid);
        $("#ppn"+newid).attr("id","ppn"+oldid);
        $("#pph"+newid).attr("id","pph"+oldid);
        $("#typepph"+newid).attr("id","typepph"+oldid);
        $("#amount_jasa"+newid).attr("id","amount_jasa"+oldid);
        $("#amount_final"+newid).attr("id","amount_final"+oldid);  
        $("#file"+newid).attr("id","file"+oldid);   

        no-=1;
        var a = no -1;

        for (var i =  ids; i <= a; i++) {   
            var newid = parseInt(i) + 1;
            var oldid = newid - 1;
            $("#"+newid).attr("id",oldid);
            $("#invoice"+newid).attr("name","invoice"+oldid);
            $("#invoice_number"+newid).attr("name","invoice_number"+oldid);
            $("#amount"+newid).attr("name","amount"+oldid);
            $("#amountppn"+newid).attr("name","amountppn"+oldid);
            $("#ppn"+newid).attr("name","ppn"+oldid);
            $("#pph"+newid).attr("name","pph"+oldid); 
            $("#typepph"+newid).attr("name","typepph"+oldid);
            $("#amount_jasa"+newid).attr("name","amount_jasa"+oldid);
            $("#amount_final"+newid).attr("name","amount_final"+oldid);  
        	$("#file"+newid).attr("name","file"+oldid);  

            $("#invoice"+newid).attr("id","invoice"+oldid);
            $("#invoice_number"+newid).attr("id","invoice_number"+oldid);
            $("#amount"+newid).attr("id","amount"+oldid);
            $("#amountppn"+newid).attr("id","amountppn"+oldid);
            $("#ppn"+newid).attr("id","ppn"+oldid);
            $("#pph"+newid).attr("id","pph"+oldid);
            $("#typepph"+newid).attr("id","typepph"+oldid);
            $("#amount_jasa"+newid).attr("id","amount_jasa"+oldid);
            $("#amount_final"+newid).attr("id","amount_final"+oldid);
        	$("#file"+newid).attr("id","file"+oldid);     
        }

        document.getElementById(lop).value = a;
        getTotal("pph"+a);
    }

    function getSupplier(elem){

        invoice_list = "";
        payment_list = "";
        supplier_name = "";

        var currency = elem.value;
        var vendor = $("#supplier_code").val();
        var invoice_number = $.parseJSON('<?php echo $invoice; ?>');

        // console.log(invoice_number)
        
        $("#invoice1").html('');
        $("#payment_term").html('');

        invoice_list += '<option value=""></option>';
        payment_list += '<option value=""></option>';
        
        for(var i = 0; i < invoice_number.length;i++){
            if (invoice_number[i].supplier_code == vendor) {
	            payment_list += '<option value="'+invoice_number[i].payment_term+'" selected>'+invoice_number[i].payment_term+'</option>';
	        }

            if (vendor === '') {
                invoice_list += '<option value=""></option>';
            }else{
                if ((invoice_number[i].supplier_code == vendor) && (invoice_number[i].currency == currency)) {
                    supplier_name = invoice_number[i].supplier_name;;
                    invoice_list += '<option value="'+invoice_number[i].id+'">'+invoice_number[i].invoice_no+'</option>';
                }

            }
        }

        for(var i = 0; i < payment_term_all.length;i++){
        	payment_list += '<option value="'+payment_term_all[i].payment_term+'">'+payment_term_all[i].payment_term+'</option>';
        }

     //    for(var i = 0; i < invoice_number.length;i++){
	    //     payment_list += '<option value="'+invoice_number[i].payment_term+'">'+invoice_number[i].payment_term+'</option>';
	    // }

        $("#invoice1").append(invoice_list);
        $("#payment_term").append(payment_list);
        $('#supplier_name').val(supplier_name);
       // console.log(payment_list)
    }

     function delete_payment(id) {
        $('#modaldelete').modal('show');
        $('[name=modalButtonDelete]').attr("id","delete_"+id);
    }

    function modaldeletehide(){
        $('#modaldelete').modal('hide');
    }

    function delete_payment_request(id){

        var id_delete = id.split("_");

        var data = {
            id:id_delete[1]
        }
        $("#loading").show();

        $.post('{{ url("delete/billing/payment_request") }}', data, function(result, status, xhr){
            if (result.status == true) {
                openSuccessGritter("Success","Data Berhasil Dihapus");
                $("#loading").hide();
                modaldeletehide();
                fetchTable();
            }
            else{
                openErrorGritter("Success","Data Gagal Dihapus");
            }
        });
    }

    function closemodal(){
        $('#modalNew').modal('hide');
    }

    function editPR(id){
    	$('#modalPR').modal("show");
    	$("#id_edit_pr").val(id);
    }

    function confirm_pr() {
      var data = {
        id: $("#id_edit_pr").val()
      };

      $.post('{{ url("payment_request/send_document") }}', data, function(result, status, xhr){
        if (result.status == true) {
            openSuccessGritter("Success","Data Berhasil Disimpan");
   			$('#modalPR').modal('hide');
            fetchTable();
        } else {
            openErrorGritter("Success","Data Gagal Disimpan");
        }
      })
    }

	function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Payment Request ini ke Manager?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("email/payment_request") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function ResendEmail(id) {

      var data = {
        id:id
      };

      if (!confirm("Gunakan Fitur ini untuk mengirim email reminder ke pihak approver. Mohon untuk tidak melakukan spam. Apakah anda yakin ingin mengirim email reminder ini ke approver?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("payment_request/resendemail") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Resend Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function cekPPN(elem){
    	var num = elem.id.match(/\d/g);
        num = num.join("");
	    if ($('#ppncheck'+num).prop('checked')) {
		    $('#ppn'+num).val("11");
	    }else{
		    $('#ppn'+num).val("0");
	    }
    }

    function withPPN(elem) {
        var num = elem.id.match(/\d/g);
        num = num.join("");

	    // if ($('#ppn'+num).prop('checked')) {
		//     $('#ppn'+num).val("on");
	    // }else{
		//     $('#ppn'+num).val("");
	    // }

    	$('#ppn'+num).val(elem.value);
	    $('#typepph'+num).val("").trigger('change');
	    $('#amount_jasa'+num).val("");
	    $('#pph'+num).val("");
	    $('#amount_final'+num).val("");
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

