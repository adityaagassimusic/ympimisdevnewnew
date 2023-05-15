@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

<style type="text/css">
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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	
	table.table-bordered > tbody > tr:hover > td {
		background-color: #FFD700;
		cursor: pointer;
	}

	#loading, #error { display: none; }

	#no_po {
		text-align: center;
		font-weight: bold;
	}
	#lot {
		text-align: center;
		font-weight: bold;
	}
	#z1 {
		text-align: center;
		font-weight: bold;
	}
	#total {
		text-align: center;
		font-weight: bold;
	}
	#progress-text {
		text-align: center;
		font-weight: bold;
		font-size: 1.5vw;
		color: #fff;
	}

	#loading, #error { display: none; }

	.redclass{
		background-color: #f15c80 !important;
		color: white;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row" style="margin-left: 1%; margin-right: 1%;" id="main">
		<div class="col-xs-6 col-xs-offset-3" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<select class="form-control select2" id="no_po" name="no_po" style="width: 100%;" data-placeholder="Pilih Nomor PO" onchange="cekPO()">
	              <option value=""></option>
	              @foreach($po_detail as $po)
	              <option value="{{ $po->no_po }}">{{ $po->no_po }}</option>
	              @endforeach
	            </select>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>

						<th style="width:15%; background-color: #ef6c00; text-align: center; color: white; padding:0;font-size: 25px;" colspan="9" id='po_title'>Nomor PO Kantin</th>
					</tr>
					<tr>
						<th width="1%" style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">Nomor</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO PO</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO ITEM</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">DETAIL ITEM</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY RECEIVE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">RECEIVE DATE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">SURAT JALAN</th>
					</tr>
				</thead>
				<tbody id="po_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="confirm">
			<br>
			<div class="col-xs-6 pull-right" align="right" style="padding: 0px;">
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 15%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" class="btn btn-success"><i class="fa fa-edit"></i> SUBMIT</button>
			</div>
			<div class="col-xs-3 pull-right" align="right" style="padding: 0px;">
			</div>
		</div>



		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;">
			
	    <hr style="color:red"> 
			<div class="row">
				<div class="col-xs-12">
					<div class="col-xs-3" style="padding:0">
						<div class="form-group">
							<label style="color:white">Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="tanggal" name="tanggal" onchange="fetchTable()">
							</div>
						</div>
					</div>
					<div class="col-xs-3" style="padding-right:0">
						<div class="form-group">
							<label style="color:white">Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="tanggal_ke" name="tanggal_ke" onchange="fetchTable()">
							</div>
						</div>
					</div>
				</div>
			</div>

			<table class="table table-bordered" id="itemtable">
				<thead>
					<tr>
						<th style="width:15%; background-color: #673ab7; text-align: center; color: white; padding:0;font-size: 25px;" colspan="9" id='po_title'>Forecast / Prediksi Kedatangan Barang</th>
					</tr>
					<tr>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO PO</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO PR</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">DETAIL ITEM</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">DELIVERY DATE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">GOODS PRICE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY RECEIVE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">RECEIVE DATE</th>
						<th style="text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">SURAT JALAN</th>
					</tr>
				</thead>
				<tbody id="po_body">
				</tbody>
			</table>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
		$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


	var vdo;
	
	jQuery(document).ready(function() {

		$('#no_po').blur();

		$('#confirm').hide();

		$('#tanggal').datepicker({
        	autoclose: true,
        	todayHighlight: true,
      		format: "yyyy-mm-dd"
        });

        $('#tanggal_ke').datepicker({
        	autoclose: true,
        	todayHighlight: true,
      		format: "yyyy-mm-dd"
        });

        fetchTable();

	});

	$('.select2').select2({
	  	allowClear: true,
	  	dropdownAutoWidth : true
	});

	$('#no_po').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var id = $("#no_po").val();
			checkCode(id);

		}
	});

	var total;

	function cekPO(){
		var id = $("#no_po").val();
		checkCode(id);
	}



	function checkCode(code) {

		var data = {
			no_po : code
		}

		$.get('{{ url("fetch/ga/receive_kantin") }}', data, function(result, status, xhr){

			if (result.status) {
				if(result.datas.length > 0){
					$("#po_body").append().empty();
					list = [];


					$.each(result.datas, function(index, value){

						$(".modal-backdrop").remove();
						var fillList = true;

						if(fillList){

							list.push({
								'id' : value.id, 
								'no_po' : value.no_po, 
								'no_item' : value.no_item, 
								'nama_item' : value.nama_item, 
								'qty' : value.qty,
								'qty_receive' : value.qty_receive,
								'date_receive' : value.date_receive,
								'surat_jalan' : value.surat_jalan
							});
						}
					});

					canc();
					fillStore();

				} else {
					canc();
					openErrorGritter('Error', 'Nomor PO Tidak Terdaftar');
				}			


			} else {
				canc();
				openErrorGritter('Error', 'Nomor PO Tidak Terdaftar');
			}

			$(".modal-backdrop").remove();
		});

	}


	function fillStore(){


		$('input:checkbox').prop('checked', false);
		$('#picked').html(0);
		total = 0;


		var body = '';
		var css = 'style="background-color: #000000;"';

		$("#po_body").empty();

		var num = '';
		for (var i = 0; i < list.length; i++) {
			var css = 'style="padding: 10px; text-align: center; color: #000000; font-size: 15px;"';
			var id = 'id="'+list[i].id+'"';

			num++;
			body += '<tr '+id+'>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+num+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].no_po+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].no_item+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].nama_item+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].qty+'</td>';

			if (list[i].qty_receive == 0) 
			{
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="hidden" name="no_po_'+list[i].id+'" id="no_po_'+list[i].id+'" value="'+list[i].no_po+'"><input type="hidden" name="no_item_'+list[i].id+'" id="no_item_'+list[i].id+'" value="'+list[i].no_item+'"> <input type="text" name="qty_receive_'+list[i].id+'" id="qty_receive_'+list[i].id+'" class="form-control qty" placeholder="Qty Receive"> </td>';
			}
			else if (list[i].qty != list[i].qty_receive) 
			{
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="hidden" name="no_po_'+list[i].id+'" id="no_po_'+list[i].id+'" value="'+list[i].no_po+'"><input type="hidden" name="no_item_'+list[i].id+'" id="no_item_'+list[i].id+'" value="'+list[i].no_item+'">Sudah Diinput : '+list[i].qty_receive+' <br><input type="text" name="qty_receive_'+list[i].id+'" id="qty_receive_'+list[i].id+'" class="form-control qty" placeholder="Qty Receive"> </td>';	
			}
			else{
				body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].qty_receive+'</td>';				
			}

			if (list[i].qty_receive == 0) 
			{
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="text" class="form-control pull-right datepicker dt" id="date_receive_'+list[i].id+'" name="date_receive_'+list[i].id+'" placeholder="Date Receive"></td>';
			}
			else if (list[i].qty != list[i].qty_receive) {
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="text" class="form-control pull-right datepicker dt" id="date_receive_'+list[i].id+'" name="date_receive_'+list[i].id+'" placeholder="Date Receive"></td>';
			}
			else
			{
				body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].date_receive+'</td>';				
			}

			if (list[i].qty_receive == 0)
			{
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="text" class="form-control pull-right" id="surat_jalan_'+list[i].id+'" name="surat_jalan_'+list[i].id+'" placeholder="Surat Jalan"></td>';
			}
			else if (list[i].qty != list[i].qty_receive) {
				body += '<td '+id+' onclick="countPicked(this)" style="padding:10px;text-align:left"><input type="text" class="form-control pull-right" id="surat_jalan_'+list[i].id+'" name="surat_jalan_'+list[i].id+'" placeholder="Surat Jalan" value='+list[i].surat_jalan+'></td>';
			}
			else
			{
				body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].surat_jalan+'</td>';				
			}
			body += '</tr>';

		}
		$("#po_body").append(body);

		if(list.length > 0){
			$('#confirm').show();
		}

		$('.datepicker').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
			orientation: 'bottom auto',
		});
	}

	function printLabel(element){
		var tag = [];
		$("input[type=checkbox]:checked").each(function() {
			var id_print = this.id.split("_");
			tag.push(id_print[1]);
		});

		if(tag.length < 1){
			alert("Item Picked 0");
			return false;
		}

		for (var i = 0; i < tag.length; i++) {	
			// console.log(tag[i]);
			// console.log(tag.length);
			print_label(tag[i], ('Print Item'+tag[i]));
		}

	}

	function print_label(id,windowName) {
		newwindow = window.open('{{ url("print/warehouse/label") }}'+'/'+id, windowName, 'height=250,width=450');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}


	function countPicked(element){

		var id = $(element).attr("id");
		// console.log(id);

		var checkDisabled = $('#print_'+id).prop("disabled");
		// console.log(checkDisabled);

		if(checkDisabled == undefined){

		}
		else{
			var checkVal = $('#print_'+id).is(":checked");
			// console.log(checkVal);
			if(checkVal) {
				total--;
				$('#print_'+ String(id)).prop('checked', false);

			}else{
				total++;
				$('#print_'+ String(id)).prop('checked', true);
			}


		}
		// console.log(total);
		
		$("#picked").html(total);
	}	

	function canc(){
		$('#no_po').val("");
		$('#no_po').focus();
		$('#no_po').blur();

	}

	function conf() {
		$("#loading").show();

		var arr_params = [];

		$('.qty').each(function(index, value) {
			ids = $(this).attr('id').split('_');
			if ($(this).val() != ""){
				arr_params.push({
					'id' : ids[2], 
					'qty' : $(this).val(), 
					'date' : $('#date_receive_'+ids[2]).val(), 
					'surat_jalan' : $('#surat_jalan_'+ids[2]).val(),
					'no_po' : $('#no_po_'+ids[2]).val(),
					'no_item' : $('#no_item_'+ids[2]).val(),
				});
			}
		});

		var data = {
			item : arr_params
		}

		if(confirm("Data akan simpan oleh sistem.\nData tidak dapat dikembalikan.")){
			$.post('{{ url("fetch/ga/update_receive_kantin") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);
					fetchTable();
					$("#po_body").empty();
					$('#confirm').hide();
					$("#loading").hide();
					
					list = [];
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
	}

	function fetchTable(){
		$('#itemtable').DataTable().destroy();

		var tanggal = $('#tanggal').val();
		var tanggal_ke = $('#tanggal_ke').val();

		var data = {
			tanggal:tanggal,
			tanggal_ke:tanggal_ke
		}

		var table = $('#itemtable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 50,
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
				"url" : "{{ url('fetch/ga/outstanding_kedatangan_kantin') }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_po"},
			{ "data": "no_pr"},
			{ "data": "nama_item"},
			{ "data": "qty"},
			{ "data": "delivery_date"},
			{ "data": "goods_price","className" : "text-right"},
			{ "data": "qty_receive"},
			{ "data": "date_receive"},
			{ "data": "surat_jalan"}
			],
			"createdRow":  function( row, data, dataIndex ) {
   				if (data['qty_receive'] == "-") {
      				$(row.children).addClass('redclass');
	    		}else if(data['qty'] != data['qty_receive']){
      				$(row.children).addClass('bg-yellow');
	    		} else{
      				$(row.children).addClass('bg-green');
	    		} 		
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection