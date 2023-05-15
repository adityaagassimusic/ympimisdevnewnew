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
	  text-align:left;
	  overflow:hidden;
	  padding: 3px;
	}
	tbody>tr>td{
	  text-align:left;
	}
	tfoot>tr>th{
	  text-align:left;
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
	<h1>
		Tools/Equipment Order <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<!-- <li>
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create PR</a>
		</li> -->
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
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body">
							<table id="toolTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:2%;">Item Code</th>
										<th style="width:10%;">Description</th>
										<th style="width:3%;">Rack Code</th>
										<th style="width:3%;">Location</th>
										<th style="width:3%;">Group</th>
										<th style="width:1%;">Qty Order</th>
										<th style="width:1%;">Month</th>
										<!-- <th style="width:5%;">N</th> -->
										<th style="width:5%;">Status</th>
									</tr>
								</thead>
								<tbody id="tableBodyResult">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>NG Rate Operator Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12" style="margin-bottom: 20px;">
							<div class="col-md-6">
								<h5 class="modal-title">NG Rate</h5><br>
								<h5 class="modal-title" id="ng_rate"></h5>
							</div>
							<div class="col-md-6">
								<div id="modal_ng" style="height: 200px"></div>
							</div>
						</div>

						<div class="col-md-8">
							<table id="welding-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="welding-ng-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="6" style="text-align: center;">NOT GOOD</th>
									</tr>
									<tr>
										<th style="width: 15%;">Finish Welding</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>NG Name</th>
										<th style="width: 5%;">Material Qty</th>
									</tr>
								</thead>
								<tbody id="welding-ng-log-body">
								</tbody>
							</table>
						</div>

						<div class="col-md-6">
							<table id="welding-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="welding-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">GOOD</th>
									</tr>
									<tr>
										<th>Finish Welding</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
									</tr>
								</thead>
								<tbody id="welding-log-body">
								</tbody>
							</table>
						</div>
						<div class="col-md-6">
							<table id="welding-cek" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="welding-cek-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">TOTAL CEK</th>
									</tr>
									<tr>
										<th>Finish Welding</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
									</tr>
								</thead>
								<tbody id="welding-cek-body">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<form id="importForm" name="importForm" method="post" action="{{ url('tools/create/purchase_requisition') }}" enctype="multipart/form-data">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal fade" id="modalCreate">
			<div class="modal-dialog modal-lg" style="width: 1300px">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Create Purchase Requisition</h4>
						<br>
						<div class="nav-tabs-custom tab-danger">
							<ul class="nav nav-tabs">
								<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Create PR</a></li>
							</ul>
						</div>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-3">
											<div class="form-group">
												<label>Identitas<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
												<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
												<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Departemen<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->department}} {{$employee->section}}" readonly="">
												<input type="hidden" id="department" name="department" value="{{$employee->department}}">
												<input type="hidden" id="section" name="section" value="{{$employee->section}}">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Tanggal Pengajuan<span class="text-red">*</span></label>
												<div class="input-group date">
													<div class="input-group-addon">	
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control pull-right" id="sd" name="sd" value="<?= date('d F Y')?>" disabled="">
													<input type="hidden" class="form-control pull-right" id="submission_date" name="submission_date" value="<?= date('Y-m-d')?>" readonly="">
												</div>
											</div>										
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Nomor PR<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="no_pr" name="no_pr" readonly="">
											</div>	
										</div>
									</div>

									<div class="col-md-12">
										<div class="col-md-6">
											<div class="form-group" id="budget_data">
												<label>Budget<span class="text-red">*</span></label>
												<select class="form-control select10" data-placeholder="Pilih Nomor Budget" name="budget_no" id="budget_no" style="width: 100% height: 35px;" required onchange="pilihBudget(this)"> 
													<option></option>
												</select>
											</div>
										</div>
										<div class="col-xs-6">
											<table class="table" style="border:none">
												<tr>
													<label>Sisa Budget Bulan Ini<span class="text-red">*</span></label>
													<td style="border:none;text-align: left;padding-left: 0;width: 30%">Jumlah Di Periode <span class="periode"></td>
													<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
													<td style="border:none;text-align: left;padding-left: 0;width: 68%"><label id="budget_sisa" name="budget_sisa"></label></td>
												</tr>
											</table>
										</div>
									</div>
									<div class="col-xs-12">
							          <div class="row">
							            <hr style="border: 1px solid red;background-color: red">
							          </div>
							        </div>
									<div class="col-md-12">
										<div class="col-xs-1" style="padding:5px;">
											<b>Kode Item</b>
										</div>
										<div class="col-xs-4" style="padding:5px;">
											<b>Deskripsi</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Tgl Kedatangan</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Mata Uang</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Harga</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Jumlah</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>UOM</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Total</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Aksi</b>
										</div>

										<input type="text" name="lop" id="lop" value="1" hidden>
										<div class="col-xs-1" style="padding:5px;">
											<select class="form-control select10" data-placeholder="Choose Item" name="item_code1" id="item_code1" style="width: 100% height: 35px;" onchange="pilihItem(this)">
											</select>
										</div>
										<div class="col-xs-4" style="padding:5px;">
											<input type="text" class="form-control" id="item_desc1" name="item_desc1" placeholder="Description" required="" onkeyup="ubahDescTujuan(this)">
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar" style="font-size: 10px"></i>
												</div>
												<input type="text" class="form-control pull-right datepicker" id="req_date1" name="req_date1" placeholder="Tanggal" required="">
											</div>
										</div>

										<div class="col-xs-1" style="padding: 5px">
											<select class="form-control select2" id="item_currency1" name="item_currency1" data-placeholder='Currency' style="width: 100%" onchange="currency(this)">
												<option value="">&nbsp;</option>
												<option value="USD">USD</option>
												<option value="IDR">IDR</option>
												<option value="JPY">JPY</option>
											</select>
											<input type="text" class="form-control" id="item_currency_text1" name="item_currency_text1" style="display:none">
										</div>

										<div class="col-xs-1" style="padding:5px;">

											<div class="input-group"> 
												<span class="input-group-addon" id="ket_harga1" name="ket_harga1" style="padding:3px">?</span>
												<input type="text" class="form-control currency" id="item_price1" name="item_price1" placeholder="Harga" data-number-to-fixed="2" data-number-stepfactor="100"required="" style="padding: 6px 6px">
											</div>
											<!-- input type="text" class="form-control" id="item_price1" name="item_price1" placeholder="Price" required="" onkeyup='getTotal(this.id)'> -->
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<input type="text" class="form-control" id="qty1" name="qty1" placeholder="Qty" required="" readonly>
											<input type="hidden" class="form-control" id="moq1" name="moq1" placeholder="Moq">
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<!-- <select class="form-control select7" id="uom1" name="uom1" data-placeholder="UOM" style="width: 100%;">
												<option></option>

											</select> -->
											<input type="text" class="form-control" id="uom1" name="uom1" placeholder="UOM">
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<input type="text" class="form-control" id="amount1" name="amount1" placeholder="Total" required="" readonly="">
											<input type="hidden" class="form-control" id="konversi_dollar1" name="konversi_dollar1" placeholder="Total" required="" readonly="">
										</div>						          		
										<div class="col-xs-1" style="padding:5px;">
											<a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></a>
										</div>	
									</div>

									<div id="tambah"></div>

									<div class="col-md-12">
										<div class="col-xs-2" style="padding:5px;">
											<input type="text" class="form-control" id="tujuan_desc1" name="tujuan_desc1" placeholder="Description" required="">
										</div>
										<div class="col-xs-6" style="padding:5px;">
											<input type="text" class="form-control" id="tujuan_peruntukan1" name="tujuan_peruntukan1" placeholder="Tujuan Pembelian / Peruntukan">
										</div>
										<div class="col-xs-2" style="padding:5px;">
											<input type="text" class="form-control" id="item_stock1" name="item_stock1" placeholder="Stock">
										</div>
										<div class="col-xs-2" style="padding:5px;">
											<input type="text" class="form-control" id="tujuan_kebutuhan1" name="tujuan_kebutuhan1" placeholder="Kebutuhan (e.g. 10 pcs/hari)">
										</div>
									</div>
									
									<div id="peruntukan"></div>



									<div class="col-md-2 col-md-offset-6">
										<p><b>Total Dollar</b></p>
										<div class="input-group">
											<span class="input-group-addon">$ </span><input type="text" id="total_usd" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-2">
										<p><b>Total Rupiah</b></p>
										<div class="input-group">
											<span class="input-group-addon">Rp. </span><input type="text" id="total_id" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-2">
										<p><b>Total Yen</b></p>
										<div class="input-group">
											<span class="input-group-addon">¥ </span><input type="text" id="total_yen" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-3 col-md-offset-9" style="margin-top: 20px">
										<p><b>Total Keseluruhan</b></p>
										<div class="input-group">
											<span class="input-group-addon">$ </span><input type="text" id="total_keseluruhan" class="form-control" readonly>
										</div>
									</div>

									<div class="col-md-11" style="margin-top: 20px">
										<p><b>Informasi Budget</b></p>
										<table class="table table-striped text-center">
											<tr>
												<th>Bulan</th>
												<th>Saldo Awal</th>
												<th>Total Pembelian</th>
												<th>Saldo Akhir</th>
											</tr>
											<tr>
												<td>
													<label id="bulanbudget" name="bulanbudget"></label>
												</td>
												<td>
													<label id="budgetLabel" name="budgetLabel"></label>
													<input type="hidden" id="budget" name="budget">
												</td>
												<td>
													<label id="TotalPembelianLabel" name="TotalPembelianLabel"></label>
													<input type="hidden" id="TotalPembelian" name="TotalPembelian">
												</td>
												<td>
													<label id="SisaBudgetLabel" name="SisaBudgetLabel"></label>
												</td>
											</tr>
										</table>
									</div>
									
									<div class="col-md-12">
										<button type="submit" class="btn btn-success pull-right">Konfirmasi</button> 
										<span class="pull-right">&nbsp;</span>
										<a class="btn btn-primary btnPrevious pull-right">Kembali</a>

									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>


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

	var no = 2;
	var limitdate = "";
	hasil_konversi_yen = 0;
	hasil_konversi_id = 0;
	item = [];
	item_list = "";
	total_usd = 0;
	total_id = 0;
	total_yen = 0;
	exchange_rate = [];


	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		$('body').toggleClass("sidebar-collapse");
		fillTable();

		$('.select10').select2({
			dropdownAutoWidth : true,
			dropdownParent: $("#budget_data"),
			allowClear:true,
		});

		getItemList();
		getExchangeRate();
	});

	function clearSearch(){
		location.reload(true);
	}

	$('.datepicker').datepicker({
    	autoclose: true,
    	format: 'yyyy-mm-dd',
    	startDate: limitdate,
    	todayHighlight: true
    });


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
	    return monthNames[month] + '-' + year;

	  }

	function fillTable(){
	    $('#loading').show();

	    $.get('{{ url("fetch/tools/need_order") }}', function(result, status, xhr){
	      $('#toolTable').DataTable().clear();
	      $('#toolTable').DataTable().destroy();
	      $('#tableBodyResult').html("");
	      var tableData = "";

	      $.each(result.resume, function(key, value) {
	       tableData += '<tr>';     
	       tableData += '<td>'+ value.item_code+'</td>';
	       tableData += '<td>'+ value.description+'</td>';
	       tableData += '<td>'+ value.rack_code +'</td>';
	       tableData += '<td>'+ value.location +'</td>';
	       tableData += '<td>'+ value.group +'</td>';
	       tableData += '<td>'+ value.qty +'</td>';
	       tableData += '<td>January</td>';
	        if (value.status == "waiting_order") {
				tableData += '<td style="background-color:#b02828;color:white">Menunggu Di Order</td>';
			} else if (value.status == "pr_approval") {
				tableData += '<td style="background-color:#f57f17;color:white">Approval PR</td>';
			} else if (value.status == "po_confirmed") {
				tableData += '<td style="background-color:#01579b;color:white">Konfirmasi PO</td>';
			} else if (value.status == "received") {
				tableData += '<td style="background-color:#00af50;color:white">Diterima Gudang</td>';
			}
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
	        'pageLength': 15,
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

	function openModalCreate(){
		$('#modalCreate').modal('show');

		//nomor PR auto generate
		var nomorpr = document.getElementById("no_pr");

		$.ajax({
			url: "{{ url('purchase_requisition/get_nomor_pr') }}?dept=<?= $employee->department ?>&sect=<?= $employee->section ?>&group=<?= $employee->group ?>", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				var no = obj.no_urut;
				var tahun = obj.tahun;
				var bulan = obj.bulan;
				var dept = obj.dept;

				nomorpr.value = dept+tahun+bulan+no;
			}
		});

		getBudget();
	}

	function getBudget() {

		data = {
			department : "{{ $employee->department }}",
		}
		
		$.get('{{ url("fetch/purchase_requisition/budgetlist") }}', data, function(result, status, xhr) {
	  		budget_list = "";
	  		budget_list = "<option value=''></option>";
			$.each(result.budget, function(index, value){
				budget_list += "<option value="+value.budget_no+">"+value.budget_no+" - "+value.description+"</option> ";
			});
			// console.log($('#budget_no').val());


			if ($('#budget_no').val() == "" || $('#budget_no').val() == null) {
	  			$('#budget_no').html('');
				$('#budget_no').append(budget_list);				
			}

		})
	}

	function pilihBudget(elem)
	{
		$.ajax({

			url: "{{ route('admin.prgetbudgetdesc') }}?budget_no="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('.periode').text(obj.periode);

				$('#budget_sisa').text("$"+obj.budget_now.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				$('#budgetLabel').text("$"+obj.budget_now.toFixed(2));
				$('#budget').val(obj.budget_now.toFixed(2));
				$('#bulanbudget').text(obj.namabulan);

			} 
		});
	}

	function getItemList() {
		$.get('{{ url("fetch/tools/itemlist") }}', function(result, status, xhr) {
			item_list += "<option></option> ";
			$.each(result.item, function(index, value){
				item_list += "<option value="+value.kode_item+">"+value.kode_item+ " - " +value.deskripsi+"</option> ";
			});
			$('#item_code1').append(item_list);
		})
	}

	function getExchangeRate(){
		$.ajax({
			url: "{{ url('purchase_requisition/get_exchange_rate') }}", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				for (var i = 0; i < obj.length; i++) {
            		var currency = obj[i].currency; // currency
	            	var rate = obj[i].rate; //nilai tukar

	            	exchange_rate.push({
	            		'currency' :  obj[i].currency, 
	            		'rate' :  obj[i].rate,
	            	});
	            }
	        }
	    });
	}

	function pilihItem(elem)
	{

		if($('#budget_no').val() == ""){
			$('#budget_no').val("");
      		openErrorGritter("Error","Budget Harus Diisi Dulu!!");
			return false;
		}

		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ url('fetch/tools/get_detailitem') }}?kode_item="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#item_desc'+no).val(obj.deskripsi).attr('readonly', true);
				$('#item_price'+no).val(obj.price).attr('readonly', true);
				$('#uom'+no).val(obj.uom).change().attr('readonly', true);
				$('#qty'+no).val(obj.qty).attr('readonly', true);
				$('#moq'+no).val(obj.moq);
				$('#amount'+no).val("0");
				$('#item_currency'+no).next(".select2-container").hide();
				$('#item_currency'+no).hide();
				$('#item_currency_text'+no).show();
				$('#item_currency_text'+no).val(obj.currency).show().attr('readonly', true);

				if (obj.currency == "USD") {
					$('#ket_harga'+no).text("$");
				}else if (obj.currency == "JPY") {
					$('#ket_harga'+no).text("¥");
				}else if (obj.currency == "IDR"){
					$('#ket_harga'+no).text("Rp.");
				}

				//tujuan + kebutuhan
				$('#tujuan_desc'+no).val(obj.deskripsi).attr('readonly', true);
				$('#tujuan_peruntukan'+no).val(obj.peruntukan);
				$('#tujuan_kebutuhan'+no).val(obj.kebutuhan);

				var $datepicker = $('#req_date'+no).attr('readonly', false);
				$datepicker.datepicker();
				$datepicker.datepicker('setDate', limitdate);

				getTotal(no);
			} 
		});

	    // alert(sel.value);
	}

	function getTotal(id) {
		// console.log(id);
		var num = id.match(/\d/g);
		num = num.join("");

		var price = document.getElementById("item_price"+num).value;
	    // var prc = price.replace(/\D/g, ""); get angka saja

	    var qty = document.getElementById("qty"+num).value;
      	// var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty
      	var hasil = parseFloat(qty) * parseFloat(price);

      	var moq = document.getElementById("moq"+num).value;

      	if (parseFloat(qty) < parseFloat(moq) && parseFloat(qty) > 0) {
      		openErrorGritter("Error","Jumlah Kurang Dari Minimum Order. Minimum order = "+moq);
      		// return false;
      	}

      	if (!isNaN(hasil)) {

    		//isi amount + amount dirubah
    		var amount = document.getElementById('amount'+num);
    		// amount.value = rubah(hasil);
    		amount.value = hasil.toFixed(2);

    		total_usd = 0;
    		total_id = 0;
    		total_yen = 0;
    		total_usd_all = 0;
    		total_yen_all = 0;
    		total_id_all = 0;
    		total_yen_konversi = 0;
    		total_id_konversi = 0;
    		total_beli = 0;

        	//mata uang

        	for (var i = 1; i < no; i++) {
        		var req_date = $('#submission_date').val();
        		date_js = new Date(req_date);
        		req_bulan = date_js.getMonth()+1;

        		var mata_uang = $('#item_currency'+i).val();
        		var mata_uang_text = $('#item_currency_text'+i).val();
        		var dollar = document.getElementById('konversi_dollar'+i);

        		tot_usd = document.getElementById('amount'+i).value;
        		tot_yen = document.getElementById('amount'+i).value;
        		tot_id = document.getElementById('amount'+i).value;

        		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
        			// total_usd += parseFloat(tot_usd.replace(/\D/g, ""));
        			// total_konversi = parseFloat(tot_usd.replace(/\D/g, ""));

        			total_usd += parseFloat(tot_usd);
        			total_konversi = parseFloat(tot_usd);
        			dollar.value = konversi("USD","USD",total_konversi);
        		}
        		else if (mata_uang == "JPY" || mata_uang_text == "JPY"){
        			total_yen += parseFloat(tot_yen);

        			total_konversi = parseFloat(tot_yen);
        			dollar.value = konversi("JPY","USD",total_konversi);

        			total_yen_konversi = parseFloat(konversi("JPY","USD",total_konversi));
        		}
        		else if (mata_uang == "IDR" || mata_uang_text == "IDR"){
        			total_id += parseFloat(tot_id);	

        			total_konversi = parseFloat(tot_id);
        			dollar.value = konversi("IDR","USD",total_konversi);   	    			

        			total_id_konversi = parseFloat(konversi("IDR","USD",total_id));
        		}

        		document.getElementById('total_usd').value = rubah(total_usd);
        		document.getElementById('total_yen').value = rubah(total_yen);
        		document.getElementById('total_id').value = rubah(total_id);

        		total_beli = total_usd + total_yen_konversi + total_id_konversi;
        		budget = $('#budget').val();

        		if (total_beli > 0) {
        			$('#TotalPembelianLabel').text("$"+total_beli.toFixed(2));
        			$('#TotalPembelian').val(total_beli.toFixed(2));
        		}else{
        			$('#TotalPembelianLabel').text("");
        		}

        		var sisa = parseFloat(budget) - parseFloat(total_beli);

        		if (total_beli > 0) {
        			if (sisa < 0) {
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2)).css("color", "red");   	    		
        			}else if(sisa > 0){
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2)).css("color", "green");
        			}
        			else{
        				$('#SisaBudgetLabel').text("$"+sisa.toFixed(2));
        			}
        		}
        		else{
        			$('#SisaBudgetLabel').text("");
        		}

        		var curr;
        		if (mata_uang != "") {
        			curr = mata_uang;
        		}
        		else if(mata_uang_text != ""){
        			curr = mata_uang_text;
        		}

        		document.getElementById('total_keseluruhan').value = konversiToUSD(curr,'USD');
        	}
        }
    }

    function tambah(id,lop) {
    	var id = id;

    	var lop = "";

    	if (id == "tambah"){
    		lop = "lop";
    	}else{
    		lop = "lop2";
    	}

    	var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Choose Item' name='item_code"+no+"' id='item_code"+no+"' onchange='pilihItem(this)'><option></option></select></div><div class='col-xs-4' style='padding:5px;'><input type='text' class='form-control' id='item_desc"+no+"' name='item_desc"+no+"' placeholder='Description' required='' onkeyup='ubahDescTujuan(this)'></div><div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date"+no+"' name='req_date"+no+"' placeholder='Tanggal' required=''></div></div> <div class='col-xs-1' style='padding: 5px'><select class='form-control select2' id='item_currency"+no+"' name='item_currency"+no+"'data-placeholder='Currency' style='width: 100%' onchange='currency(this)'><option value=''>&nbsp;</option><option value='USD'>USD</option><option value='IDR'>IDR</option><option value='JPY'>JPY</option></select><input type='text' class='form-control' id='item_currency_text"+no+"' name='item_currency_text"+no+"' style='display:none'></div> <div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"' name='ket_harga"+no+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price"+no+"' name='item_price"+no+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' style='padding:6px 6px'></div></div><div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' required=''><input type='hidden' class='form-control' id='moq"+no+"' name='moq"+no+"' placeholder='Moq'></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='uom"+no+"' name='uom"+no+"' placeholder='UOM'></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' placeholder='Total' required='' readonly><input type='hidden' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Total' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

    	var tujuan = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='tujuan_desc"+no+"' name='tujuan_desc"+no+"' placeholder='Description' required=''></div><div class='col-xs-6' style='padding:5px;'><input type='text' class='form-control' id='tujuan_peruntukan"+no+"' name='tujuan_peruntukan"+no+"' placeholder='Tujuan Pembelian / Peruntukan'></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_stock"+no+"' name='item_stock"+no+"' placeholder='Stock'></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='tujuan_kebutuhan"+no+"' name='tujuan_kebutuhan"+no+"' placeholder='Kebutuhan (e.g 10 pcs/hari)'></div></div>");

    	$("#"+id).append(divdata);

    	if (id == "tambah"){
    		$("#peruntukan").append(tujuan);
		}else{
    		$("#peruntukan_edit").append(tujuan);
		}
    	$("#item_code"+no).append(item_list);


    	$('.datepicker').datepicker({
    		autoclose: true,
    		format: 'yyyy-mm-dd',
    		startDate: limitdate,
    		todayHighlight:true
    	});

    	$(function () {
    		$('.select3').select2({
    			dropdownAutoWidth : true,
    			dropdownParent: $("#"+id),
    			allowClear:true
    		});


		  	$('.select6').select2({
    			dropdownParent: $("#"+id),
		  		allowClear:true,
		  		dropdownAutoWidth : true,
		  	});
    	})

		// $("#"+id).select2().trigger('change');
		document.getElementById(lop).value = no;
		no+=1;
	}

	//Fungsi Kurang

	function kurang(elem,lop) {

		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		$('#tujuan_desc'+ids).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;

		$("#"+newid).attr("id",oldid);
		$("#item_code"+newid).attr("name","item_code"+oldid);
		$("#item_desc"+newid).attr("name","item_desc"+oldid);
		// $("#item_spec"+newid).attr("name","item_spec"+oldid);
		$("#item_price"+newid).attr("name","item_price"+oldid);
		$("#qty"+newid).attr("name","qty"+oldid);
		$("#moq"+newid).attr("name","moq"+oldid);
		$("#uom"+newid).attr("name","uom"+oldid);
		$("#ket_harga"+newid).attr("name","ket_harga"+oldid);
		$("#amount"+newid).attr("name","amount"+oldid);
		$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
		$("#item_currency"+newid).attr("name","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
		$("#req_date"+newid).attr("name","req_date"+oldid);
		$("#tujuan_desc"+newid).attr("name","tujuan_desc"+oldid);
		$("#tujuan_peruntukan"+newid).attr("name","tujuan_peruntukan"+oldid);
		$("#item_stock"+newid).attr("name","item_stock"+oldid);
		$("#tujuan_kebutuhan"+newid).attr("name","tujuan_kebutuhan"+oldid);

		$("#item_code"+newid).attr("id","item_code"+oldid);
		$("#item_desc"+newid).attr("id","item_desc"+oldid);
		// $("#item_spec"+newid).attr("id","item_spec"+oldid);
		$("#item_price"+newid).attr("id","item_price"+oldid);
		$("#qty"+newid).attr("id","qty"+oldid);
		$("#moq"+newid).attr("id","moq"+oldid);
		$("#uom"+newid).attr("id","uom"+oldid);
		$("#ket_harga"+newid).attr("id","ket_harga"+oldid);
		$("#amount"+newid).attr("id","amount"+oldid);
		$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
		$("#item_currency"+newid).attr("id","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
		$("#req_date"+newid).attr("id","req_date"+oldid);
		$("#tujuan_desc"+newid).attr("id","tujuan_desc"+oldid);
		$("#tujuan_peruntukan"+newid).attr("id","tujuan_peruntukan"+oldid);
		$("#item_stock"+newid).attr("id","item_stock"+oldid);
		$("#tujuan_kebutuhan"+newid).attr("id","tujuan_kebutuhan"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			$("#"+newid).attr("id",oldid);
			$("#item_code"+newid).attr("name","item_code"+oldid);
			$("#item_desc"+newid).attr("name","item_desc"+oldid);
			// $("#item_spec"+newid).attr("name","item_spec"+oldid);
			$("#item_price"+newid).attr("name","item_price"+oldid);
			$("#qty"+newid).attr("name","qty"+oldid);
			$("#moq"+newid).attr("name","moq"+oldid);
			$("#uom"+newid).attr("name","uom"+oldid);
			$("#ket_harga"+newid).attr("name","ket_harga"+oldid);
			$("#amount"+newid).attr("name","amount"+oldid);
			$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
			$("#item_currency"+newid).attr("name","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
			$("#req_date"+newid).attr("name","req_date"+oldid);
			$("#tujuan_desc"+newid).attr("name","tujuan_desc"+oldid);
			$("#tujuan_peruntukan"+newid).attr("name","tujuan_peruntukan"+oldid);
			$("#item_stock"+newid).attr("name","item_stock"+oldid);
			$("#tujuan_kebutuhan"+newid).attr("name","tujuan_kebutuhan"+oldid);

			$("#item_code"+newid).attr("id","item_code"+oldid);
			$("#item_desc"+newid).attr("id","item_desc"+oldid);
			// $("#item_spec"+newid).attr("id","item_spec"+oldid);
			$("#item_price"+newid).attr("id","item_price"+oldid);
			$("#qty"+newid).attr("id","qty"+oldid);
			$("#moq"+newid).attr("id","moq"+oldid);
			$("#uom"+newid).attr("id","uom"+oldid);
			$("#ket_harga"+newid).attr("id","ket_harga"+oldid);
			$("#amount"+newid).attr("id","amount"+oldid);
			$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
			$("#item_currency"+newid).attr("id","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
			$("#req_date"+newid).attr("id","req_date"+oldid);
			$("#tujuan_desc"+newid).attr("id","tujuan_desc"+oldid);
			$("#tujuan_peruntukan"+newid).attr("id","tujuan_peruntukan"+oldid);
			$("#item_stock"+newid).attr("id","item_stock"+oldid);
			$("#tujuan_kebutuhan"+newid).attr("id","tujuan_kebutuhan"+oldid);


			// alert(i)
		}
		document.getElementById(lop).value = a;

		getTotal("qty"+a);	

	}

	function konversi(from, to, amount){

      	var obj = exchange_rate;

      	for (var i = 0; i < obj.length; i++) {
    		var currency = obj[i].currency; // currency
        	var rate = obj[i].rate; //nilai tukar

        	if (from == currency) {
        		fromrate = rate;
        	}

        	if (to == currency) {
        		torate = rate;
        	}
        }
        hasil_konversi = (amount / fromrate) * torate;
        return hasil_konversi.toFixed(2);		    
    }

    function konversiToUSD(from, to){

    	var obj = exchange_rate;
    	var fromrate = 0;
    	var torate = 0;

    	for (var i = 0; i < obj.length; i++) {
    		var currency = obj[i].currency; // currency
        	var rate = obj[i].rate; //nilai tukar

        	if (from == currency) {
        		fromrate = rate;
        	}

        	if (to == currency) {
        		torate = rate;
        	}
        }
        if (from == "JPY") {
        	hasil_konversi_yen = (total_yen / fromrate) * torate;
        }
        if (from == "IDR") {
        	hasil_konversi_id = (total_id / fromrate) * torate;
        }

        var hasil= total_usd + parseFloat(hasil_konversi_yen.toFixed(2)) + parseFloat(hasil_konversi_id.toFixed(2));
        return hasil.toFixed(2);

    	// document.getElementById('total_keseluruhan').value = total_usd + parseFloat(hasil_konversi_yen.toFixed(2)) + parseFloat(hasil_konversi_id.toFixed(2));
    }

    function rubah(angka){
	  	var reverse = angka.toString().split('').reverse().join(''),
	  	ribuan = reverse.match(/\d{1,3}/g);
	  	ribuan = ribuan.join('.').split('').reverse().join('');
	  	return ribuan;
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
			time: '2000'
		});
	}


</script>
@endsection

