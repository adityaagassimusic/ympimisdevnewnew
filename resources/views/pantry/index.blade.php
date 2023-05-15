@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}

	#tableMenuList td:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tablemenuu> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #e0e0e0;
	}

	#tableResult > thead > tr > th {
		border:rgba(126,86,134,.7);
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #ddd;
	}

	/*td:hover{
		cursor: pointer;
		background-color: #7dfa8c;
		}*/

		#tablepesanan > tr:hover {
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
		input[type="radio"] {
		}

		#loading { display: none; }


		.radio {
			display: inline-block;
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

		/* Hide the browser's default radio button */
		.radio input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom radio button */
		.checkmark {
			position: absolute;
			top: 0;
			left: 0;
			height: 25px;
			width: 25px;
			background-color: #ccc;
			border-radius: 50%;
		}

		/* On mouse-over, add a grey background color */
		.radio:hover input ~ .checkmark {
			background-color: #ccc;
		}

		/* When the radio button is checked, add a blue background */
		.radio input:checked ~ .checkmark {
			background-color: #2196F3;
		}

		/* Create the indicator (the dot/circle - hidden when not checked) */
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the indicator (dot/circle) when checked */
		.radio input:checked ~ .checkmark:after {
			display: block;
		}

		/* Style the indicator (dot/circle) */
		.radio .checkmark:after {
			top: 9px;
			left: 9px;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: white;
		}
/*
	img {
	    width: 100%;
	    }*/

	</style>
	@stop
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	    <p style="position: absolute; color: White; top: 45%; left: 35%;">
	      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
	    </p>
	  </div>
	@section('header')
	<section class="content-header">
		<h1>
			{{ $title }}
			<span class="text-purple"> ({{ $title_jp }})</span>
		</h1>
		<ol class="breadcrumb">
			<li>
				<a onclick="orderlist()" data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-md" style="color:white"><i class="fa fa-list"></i>List Ordered</a>
			</li>
		</ol>

	</section>
	@stop
	@section('content')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<input type="hidden" id="data" value="data">
		<div class="row">
			<div class="col-xs-5">
				<div class="box">
					<div class="box-body">
						<span style="font-size: 20px; font-weight: bold;">Items (注文品) </span><br><br>
						<div class="row">
							<?php foreach ($menutop as $menu){ ?>
							<div class="col-md-4 col-sm-6 col-xs-12" onclick="getData({{$menu->id}})" style="cursor: pointer;">
								<div class="text-center">
									<img src="{{ url('images/minuman', $menu->gambar) }}" class="img-responsive" style="margin: auto;height: 100px">
									<p style="font-size: 1.5vw;padding: 5px">{{ $menu->menu }}</p>
								</div>
							</div>
						<!-- <div onclick="getData({{$menu->id}})" style="cursor: pointer;display: inline-block;margin-left: 20px">
							<center>
								<img src="{{ url('images/minuman', $menu->gambar) }}" alt="kopi" width="210px">
								<p style="font-size: 1.5vw;padding: 5px">{{ $menu->menu }}</p>
							</center>
						</div> -->

							<?php } ?>
						</div>
						<hr>
						<span style="font-size: 20px; font-weight: bold;">Khusus Tamu (お客様専用注文品) </span><br><br>
						<div class="row">
							<?php foreach ($menubot as $menu){ ?>
							<div class="col-md-4 col-sm-6 col-xs-12" onclick="getData({{$menu->id}})" style="cursor: pointer;">
								<div class="text-center">
									<img src="{{ url('images/minuman', $menu->gambar) }}" class="img-responsive" style="margin: auto;height: 100px">
									<p style="font-size: 1.5vw;padding: 5px">{{ $menu->menu }}</p>
								</div>
							</div>
						<!-- <div onclick="getData({{$menu->id}})" style="cursor: pointer;display: inline-block;margin-left: 20px">
							<center>
								<img src="{{ url('images/minuman', $menu->gambar) }}" alt="kopi" width="210px">
								<p style="font-size: 1.5vw;padding: 5px">{{ $menu->menu }}</p>
							</center>
						</div> -->

							<?php } ?>
						</div>
					<!-- <table class="table" id="tablemenuu">
						<thead>
							<tr>
								<th style="width: 1%;"></th>
							</tr>					
						</thead>
						<tbody>
							<tr>
								<td width="50%" onclick="getData(1)">
									<center>
										<img src="{{ url("images/minuman/teh.jpg") }}" alt="kopi" width="85%">
										<p style="font-size: 1.5vw;padding: 5px">Tea</p>
									</center>
								</td>
								<td width="50%" onclick="getData(2)">
									<center>
										<img src="{{ url("images/minuman/coffee.jpg") }}" alt="kopi" width="85%">
										<p style="font-size: 1.5vw;padding: 5px">Coffee</p>
									</center>
								</td>
							</tr>	
							<tr>
								<td width="50%" onclick="getData(3)">
									<center>
										<img src="{{ url("images/minuman/oca.jpg") }}" alt="kopi" width="85%">
										<p style="font-size: 1.5vw;padding: 5px">Oca</p>
									</center>
								</td>
								<td width="50%" onclick="getData(4)">
									<center>
										<img src="{{ url("images/minuman/air.jpg") }}" alt="air" width="85%">
										<p style="font-size: 1.5vw;padding: 5px">Water</p>
									</center>
								</td>
							</tr>
						</tbody>
					</table> -->
					<!-- <a class="btn btn-primary" href="{{ url('index/pantry/pesanan') }}" style="font-size: 20px; width: 100%; font-weight: bold; padding: 0;"> -->

						<!-- </a> -->
					</div>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="row">
					<!-- <input type="hidden" id="data"> -->
					<div class="col-xs-12">
						<span style="font-weight: bold; font-size: 16px;">Item (注文品)</span>
						<input type="text" id="menu" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" disabled>
						<input type="hidden" id="pemesan" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" value=" {{ Auth::user()->username}} ">
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;">Information (熱い/冷たい)</span>
						<div style="height: 40px;vertical-align: middle;border: 1px solid #d2d6de;">
							<label class="radio" style="margin-top: 5px;margin-left: 5px">Cold
								<input type="radio" checked="checked" id="information" name="information" value="Cold">
								<span class="checkmark"></span>
							</label>
							&nbsp;&nbsp;
							<label class="radio" style="margin-top: 5px">Hot
								<input type="radio" id="information" name="information" value="Hot">
								<span class="checkmark"></span>
							</label>
						</div>
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;">Add-Ons (砂糖・クリームの追加)</span><br>
						<div style="height: 40px;vertical-align: middle;border: 1px solid #d2d6de;">
							<label class="radio" style="margin-top: 5px;margin-left: 5px">No Creamer
								<input type="radio" checked="checked" id="keterangan" name="keterangan" value="No Creamer">
								<span class="checkmark"></span>
							</label>
							&nbsp;&nbsp;
							<label class="radio" style="margin-top: 5px">With Creamer
								<input type="radio" id="keterangan" name="keterangan" value="With Creamer">
								<span class="checkmark"></span>
							</label>
						</div>
					</div>
					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;">Sugar (砂糖) </span><br>
						<select class="form-control select2" style="width: 100%; height: 40px; font-size: 18px; text-align: center;" id="gula" name="gula" data-placeholder="Choose Amount Of Sugar" required>
							<option value='No Sugar'>No Sugar</option>
							<option value='Less Sugar'>Less Sugar</option>
							<option value='Many Sugar'>Many Sugar</option>
						</select>
					</div>
					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;">Places (場所)</span>
						<select class="form-control select2" style="width: 100%; height: 40px; font-size: 18px; text-align: center;" id="tempat" name="tempat" data-placeholder="Choose Places" required>
							<option value="<?= implode(' ', array_slice(explode(' ', Auth::user()->name), 0, 2)); ?>'s Table "><?= implode(' ', array_slice(explode(' ', Auth::user()->name), 0, 2)); ?>'s Table</option>
							<option value='Guest Room'>Guest Room</option>
							<option value='Meeting Room 1'>Meeting Room 1</option>
							<option value='Meeting Room 2'>Meeting Room 2</option>
							<option value='Meeting Room 3'>Meeting Room 3</option>
							<option value='Training Room 1'>Training Room 1</option>
							<option value='Training Room 2'>Training Room 2</option>
							<option value='Training Room 3'>Training Room 3</option>
							<option value='Filling Room'>Filling Room</option>
							<option value='Lobby'>Lobby</option>
						</select>
						<!-- <input type="text" id="pemesan" style="width: 100%; height: 50px; font-size: 24px; text-align: center;"> -->
					</div>


					<div class="col-xs-12">
						<span style="font-weight: bold; font-size: 16px;">Quantity (個数)</span>
					</div>
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-7">
								<div class="input-group">
									<div class="input-group-btn">
										<button type="button" class="btn btn-danger" style="font-size: 20px; height: 40px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
									</div>
									<!-- /btn-group -->
									<input id="addCount" style="font-size: 28px; height: 40px; text-align: center;" type="number" class="form-control" value="1">

									<div class="input-group-btn">
										<button type="button" class="btn btn-success" style="font-size: 20px; height: 40px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
									</div>
								</div>
							</div>
							<div class="col-xs-5" style="padding-bottom: 10px;">
								<button class="btn btn-primary" onclick="konfirmasi()" style="font-size:25px; width: 100%; font-weight: bold; padding: 0;">
									ORDER
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<span style="font-size: 20px; font-weight: bold;">Drink Order Data ({{ date('d F Y') }}) / 飲み物注文一覧</span>
								<table class="table table-hover table-striped table-bordered" id="tabelpesanann">
									<thead>
										<tr>
											<th style="width: 1%;">No</th>
											<th style="width: 2%;">Drinks</th>
											<th style="width: 2%;">Information</th>
											<th style="width: 2%;">Add-Ons</th>
											<th style="width: 2%;">Sugar</th>
											<th style="width: 1%;">Quantity</th>
											<th style="width: 2%;">Places</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tablepesanan">
									</tbody>
								</table>
							</div>
						</div>
						<button onclick="finalConfirm()" class="btn btn-success" class="btn btn-danger btn-sm" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
							ORDER CONFIRMATION
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Konfirmasi Pesanan</h4>
				</div>
				<div class="modal-body">
					Apakah anda yakin ingin mengkonfirmasi pesanan ini?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button onclick="finalConfirm()" href="#" type="button" class="btn btn-success">Konfirmasi</a>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="orderlist" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">List Pesanan Anda</h4>
					</div>
					<div class="modal-body">
						<div class="box-body">
							<table class="table table-hover table-bordered table-striped" id="tableResult">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Pemesan</th>
										<th>Minuman</th>
										<th>Informasi</th>
										<th>Keterangan</th>
										<th>Gula</th>
										<th>Jumlah</th>
										<th style="width: 20px">Tempat</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody id="tableBodyList">
								</tbody>
							</table>
						</div>    
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
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
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			jQuery(document).ready(function() {
				$('body').toggleClass("sidebar-collapse");
				$('.select2').select2({
					language : {
						noResults : function(params) {
							return "There is no cpar with status 'close'";
						}
					}
				});
				daftarmenu();
			});

			function plusCount(){
				$('#addCount').val(parseInt($('#addCount').val())+1);
			}

			function minusCount(){
				$('#addCount').val(parseInt($('#addCount').val())-1);
			}

			function getData(id){
				var data = {
					id : id,
				}
				$.get('{{ url("fetch/menu") }}', data, function(result, status, xhr){
					if(result.status){
				// $('#id_silver').val(result.menu.id);
				$('#menu').val(result.menu);
				$('#pemesan').val(result.pemesan);
				$('#addCount').val("1");
			}
			else{
				$("#loading").hide();
				alert('Attempt to retrieve data failed');
			}
		});
			}

			function konfirmasi(){
				$("#loading").show();
				var data = {
					pemesan : $("#pemesan").val(),
					menu : $("#menu").val(),
					informasi : $('input[id="information"]:checked').val(),
					keterangan : $('input[id="keterangan"]:checked').val(),
					gula : $("#gula").val(),
					tempat : $("#tempat").val(),
					jumlah : $('#addCount').val()
				}
				$.post('{{ url("index/pantry/inputmenu") }}', data, function(result, status, xhr){
					if(result.status){
						$('#menu').val("");
						$('#jumlah').val("1");

						daftarmenu();
						openSuccessGritter('Success', result.message);
						$("#loading").hide();
					}
					else{
						$("#loading").hide();
						openErrorGritter('Error!', result.message);
					}
				});
			}

			function orderlist() {
				var data = {
					pemesan : $("#pemesan").val()
				}

				$.get('{{ url("fetch/pantry/pesan") }}', data, function(result, status, xhr){
					if (result.status == true) {
						$("#orderlist").modal('show');
						$('#tableBodyList').html("");
						var tableData = "";

						$.each(result.pesanan, function(key, value) {
							tableData += '<tr>';
							tableData += '<td>'+ value.name +'</td>';
							tableData += '<td>'+ value.minuman +'</td>';
							tableData += '<td>'+ value.informasi +'</td>';            
							tableData += '<td>'+ value.keterangan +'</td>';
							tableData += '<td>'+ value.gula +'</td>';
							tableData += '<td>'+ value.jumlah +'</td>';
							tableData += '<td width=15%>'+ value.tempat +'</td>';
							if(value.status == "confirmed") {
								tableData += '<td width=15%><label class="label label-danger">Waiting Confirmation</td>';
							}
							else if(value.status == "proses") {
								tableData += '<td width=15%><label class="label label-primary">Making Your Orders</label></td>';
							}
							tableData += '</tr>';

						});
	          // }
	          $('#tableBodyList').append(tableData);
	      } else {
	      	$("#loading").hide();
	        // $("#orderlist").modal('show');
	    }
	})
			}

			function daftarmenu(){
				var data = {
					pemesan : $("#pemesan").val()
				}

				$.get('{{ url("fetch/pesanan") }}', data, function(result, status, xhr){
					$('#tablepesanan').html("");
					var tableData = '';
					var count = 1;

					if (result.lists.length > 0) {
						$.each(result.lists, function(key, value) {
							tableData += '<tr>';
							tableData += '<td>'+ count +'</td>';
							tableData += '<td>'+ value.minuman +'</td>';
							tableData += '<td>'+ value.informasi +'</td>';
							tableData += '<td>'+ value.keterangan +'</td>';
							tableData += '<td>'+ value.gula +'</td>';
							tableData += '<td style="text-align:right">'+ value.jumlah +'</td>';
							tableData += '<td>'+ value.tempat +'</td>';
							tableData += '<td><a href="javascript:void(0)" onclick="hapus(\''+value.id+'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a></td>';
							tableData += '</tr>';
							count += 1;		


						});
					}
					else{
						tableData += '<tr><td colspan="8" style="text-align:center">No Order</td></tr>';
					}


					$('#tablepesanan').append(tableData);
				});
			}

			function hapus(id){
				$("#loading").show();
				var data = {
					id : id,
				}
				$.post('{{ url("index/pantry/deletemenu") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success Batalkan Menu', result.message);
						$("#loading").hide();
						daftarmenu();
					}
					else{
						$("#loading").hide();
						openErrorGritter('Error!', result.message);
					}
				});
			}

			function finalConfirm(){
				$("#loading").show();

				var data = {
					pemesan : $("#pemesan").val()
				}

				
				$.post('{{ url("index/pantry/konfirmasipesanan") }}', data, function(result, status, xhr){
					if(result.status){		
						$('#jumlah').val("1");
				// console.log(result.sms);
				// $('#myModal').modal('hide');
				openSuccessGritter('Success', result.message);
				daftarmenu();
				$("#loading").hide();
			}
			else{
				$("#loading").hide();
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