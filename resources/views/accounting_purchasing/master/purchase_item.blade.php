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
		text-align:center;
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
	<h1>
		Purchasing Item List <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">

		<li>
			<a href="javascript:void(0)" onclick="openHistory()" class="btn btn-md bg-green" style="color:white"><i class="fa fa-list"></i> Cek History Pembelian Item</a>
		</li>

		<?php if(str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PCH')) { ?>
		<li>
			<a href="{{ url("index/purchase_item/create_category")}}" class="btn btn-md bg-blue" style="color:white">
				<i class="fa fa-plus"></i> Create New Item Category
			</a>
			<a href="{{ url("index/purchase_item/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New Item</a>
		</li>

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
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-3">
							<div class="form-group">
								<label>Keyword</label>
								<input type="text" class="form-control" id="keyword2" placeholder="Masukkan Kata Kunci">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Kategori</label>
								<select class="form-control select2" id="category" data-placeholder='Kategori Item' style="width: 100%">
		              <option value="">&nbsp;</option>
		              @foreach($item_category as $cat)
		              <option value="{{$cat->category_id}}">{{$cat->category_id}} - {{$cat->category_name}}</option>
		              @endforeach
		            </select>
							</div>
						</div>

						<!-- <div class="col-md-2">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" multiple="multiple" id="uom" data-placeholder="Select UOM" style="width: 100%;">
									<option></option>
									@foreach($uom as $um)
									<option value="{{ $um }}">{{ $um }}</option>
									@endforeach
								</select>
							</div>
						</div> -->

						<div class="col-md-2">
							<div class="form-group">
								<label>Penawaran</label>
								<select class="form-control select2" id="penawaran" data-placeholder="Status Penawaran" style="width: 100%;">
									<option></option>
									<option value="Ada">Ada</option>
									<option value="Tidak">Tidak</option>
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Barang Modal</label>
								<select class="form-control select2" id="barang_modal" data-placeholder="Status Barang Modal" style="width: 100%;">
									<option></option>
									<option value="Barang Modal">Barang Modal</option>
									<option value="Bukan Barang Modal">Bukan Barang Modal</option>
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body">
							<table id="itemtable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Item Code</th>
										<th style="width:2%;">Category</th>
										<th style="width:10%;">Description</th>
										<th style="width:2%;">Uom</th>
										<th style="width:4%;">Price</th>
										<th style="width:2%;">Currency</th>
										<th style="width:1%;">Lot</th>
										<th style="width:1%;">Moq</th>
										<th style="width:1%;">Lead Time</th>
										<th style="width:3%;">Gambar</th>
										<th style="width:2%;">Penawaran</th>
										<th style="width:2%;">Barang Modal</th>
										<th style="width:8%;">Action</th>
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

	<div class="modal fade" id="modalHistory">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<center><h3 style="background-color: #1da12e; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Cek History Pembelian</h3>
					</center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<div class="col-md-9">
							<div class="form-group">
								<label>Enter Keyword</label>
								<input type="text" class="form-control" id="keyword" name="keyword" placeholder="Masukkan Kode Item / Deskripsi">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-12">
									<label style="color: white;"> xxxxxxxxxxxxxxxxxx</label>
									<button id="search" onclick="fetchLog()" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<table class="table table-hover table-bordered table-striped" id="tableLog">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>No</th>
										<th>Vendor</th>
										<th>Nomor PO</th>
										<th>Nama Item</th>
										<th>Harga</th>
										<th>Tanggal PO</th>
									</tr>
								</thead>
								<tbody id="tableLogBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
     <form method="POST" id="editForm" autocomplete="off" enctype="multipart/form-data">
  <div class="modal fade" id="uploadForm">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" >
          <div class="col-xs-12" style="background-color: #469c6b">
            <h1 style="text-align: center; margin:5px; font-weight: bold; color: white;">Upload Foto</h1>
          </div>
        </div>
        <div class="col-xs-12">
    
        <div class="modal-body">
            <div class="row">            
               <div class="form-group">
               	
                  <label>Foto Item<span class="text-red">*</span></label>
                  <input type="hidden" name="codes" id="codes">
                  <input type="file" id="foto_item_upload" name="foto_item_upload" onchange="readURL2(this,'');">
                  <br>
                  <img width="250px" id="blah" src="" style="display: none" alt="your image" />
                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Submit</button>
          </div>
                   </div>

      </div>
    </div>
  </div>
        </form>


  <div class="modal fade" id="modalImage">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <div class="form-group">
            <div  name="image_show" id="image_show"></div>

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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear : true,
		});
		// fetchTable();
		$('body').toggleClass("sidebar-collapse");

	});

	$('#keyword2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fetchTable();
		}
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function openHistory(){
		$('#modalHistory').modal('show');
	}

	$('#keyword').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fetchLog();
		}
	});

	function readURL2(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("td").find("button").hide();
      // $('#btnImage'+idfile).hide();
    }

    function fetchLog(){
		$('#loading').show();
		var keyword = $('#keyword').val();

		var data = {
			keyword:keyword
		}

		$.get('{{ url("fetch/purchase_order/log_pembelian") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableLog').DataTable().clear();
				$('#tableLog').DataTable().destroy();
				$('#tableLogBody').html('');
				var tableLogBody = "";
				var no = 1;
				$.each(result.history, function(key, value){
					tableLogBody += '<tr>';
					tableLogBody += '<td>'+no+'</td>';
					tableLogBody += '<td>'+value.supplier_name+'</td>';
					tableLogBody += '<td>'+value.no_po+'</td>';
					tableLogBody += '<td>'+value.nama_item+'</td>';
					if (value.goods_price != 0 || value.goods_price != null) {
						tableLogBody += '<td>('+value.currency+') '+value.goods_price.toLocaleString()+'</td>';
					}else{
						tableLogBody += '<td>('+value.currency+') '+value.service_price.toLocaleString()+'</td>';
					}
					tableLogBody += '<td>'+value.tgl_po+'</td>';
					tableLogBody += '</tr>';
					no++;
				});
				$('#tableLogBody').append(tableLogBody);

				$('#tableLog').DataTable({
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
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	function uploadPhoto(id){
		$('#codes').val(id);
    $('#uploadForm').modal('show');
    }


  function showImage(id) {
  	console.log(id);


    $('#modalImage').modal('show');

    var images_show = "";
    $("#image_show").html("");

    images_show += '<img style="cursor:zoom-in" src="{{ url("images/purchase_item") }}/'+id+'.jpg" width="100%" >';

    $("#image_show").append(images_show);

  }


    $("form#editForm").submit(function(e){

      

      if ($("#foto_item_upload").val() == "") {
        $("#loading").hide();
        alert("Foto Harap diisi");
        $("html").scrollTop(0);
        return false;
      }
      

      if(confirm("Apakah anda yakin akan Upload Foto Terbaru ini?")){
        $("#loading").show();

      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: '{{ url("index/purchase_item/upload/foto") }}',
        type: 'POST',
        enctype: 'multipart/form-data',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function (result, status, xhr) {
          if(result.status) {
            openSuccessGritter("Success", 'Berhasil Dibuat');
            $("#loading").hide();
            location.reload();
          // setTimeout(function(){ window.location = "{{ url('index/purchase_item') }}" }, 2000); 
          } else {
            $("#create_btn").prop("disabled", false);
            $("#loading").hide();
            openErrorGritter("Error", "Incorrect photo format");
          }
        },
        function (xhr, ajaxOptions, thrownError) {
          $("#create_btn").prop("disabled", false);
          openErrorGritter(xhr.status, thrownError);
        }
      })
    }else{
      return false;
    }
  });
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

	

	function fetchTable(){
		$('#itemtable').DataTable().destroy();

		var keyword = $('#keyword2').val();
		var category = $('#category').val();
		var penawaran = $('#penawaran').val();
		var barang_modal = $('#barang_modal').val();

		var data = {
			keyword:keyword,
			category:category,
			penawaran:penawaran,
			barang_modal:barang_modal
		}
		
		$('#itemtable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );

		var table = $('#itemtable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
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
				"url" : "{{ url("fetch/purchase_item") }}",
				"data" : data
			},
			"columns": [
			{ "data": "kode_item"},
			{ "data": "kategori"},
			{ "data": "deskripsi"},
			{ "data": "uom"},
			{ "data": "harga"},
			{ "data": "currency"},
			{ "data": "lot"},
			{ "data": "moq"},
			{ "data": "leadtime"},
			{ "data": "image"},
			{ "data": "quotation"},
			{ "data": "category"},
			{ "data": "action"},
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
		
		$('#itemtable tfoot tr').appendTo('#itemtable thead');
	}


</script>
@endsection

