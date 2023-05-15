@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
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
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="form-group">
									<div class="col-xs-12" style="text-align: center; margin-bottom: 5px; background-color: #C3E991">
										<span style="font-weight: bold; font-size: 1.6vw;">{{ $title }}<br><small class="text-purple">{{ $title_jp }}</small></span>
									</div>
									<table id="TableListApproval" class="table table-bordered table-striped table-hover">
										<thead style="background-color: #BDD5EA; color: black;">
											<tr>
												<th width="1%">No</th>
												<th width="3%">Judul</th>
												<th width="3%">Approval</th>
												<th width="2%">#</th>
											</tr>
										</thead>
										<tbody id="bodyTableListApproval">
										</tbody>
										<tfoot>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalEditShow" style="z-index: 10000;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="nav-tabs-custom tab-danger" align="center">
					<h2 id="judul_kategori"></h2>
				</div>
				<div class="col-md-9" style="margin-bottom : 10px">
					<input type="text" class="form-control" id="jd_kategori" name="jd_kategori" required>
					<!-- <textarea rows="2" class="form-control" id="jd_kategori" name="jd_kategori" required></textarea> -->
					<span style="color: red">*) Lakukan Perubahan Judul Jika Ada Perubahan, Jika Tidak Hiraukan Saja.</span>
				</div>
				<div class="col-md-3" style="margin-bottom : 10px">
					<a onclick="SimpanJudul()" class="btn btn-success btn"  data-toggle="tooltip" style="width: 100%"><i class="fa fa-check-circle-o"></i> Simpan Judul Dokumen</a>
				</div>
				<div class="col-md-12" style="padding-top: 10px">
					<table id="TableDetail" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #BDD5EA; color: black;">
							<tr>
								<th width="1%">No</th>
								<th width="5%">Approval</th>
								<th width="2%">#</th>
							</tr>
						</thead>
						<tbody id="BodyTableDetail">
						</tbody>
					</table>
				</div>
				<div class="col-xs-9">
					<div id="modal_report"></div>
				</div>
				<div class="col-xs-3">	
					<a onclick="TambahApprover()" class="btn btn-success btn pull-right"  data-toggle="tooltip" title="Tambah Approver" style="width: 100%"><i class="fa fa-plus-circle"></i> Tambah Approver</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			dropdownParent: $('#modalEditShow'),
			allowClear : true,
		});
		DataList();
	});

	function DataList(){
		$.get('{{ url("fetch/kategori/approval") }}', function(result, status, xhr){
			if(result.status){
				$('#TableListApproval').DataTable().clear();
				$('#TableListApproval').DataTable().destroy();
				$('#bodyTableListApproval').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {

					var urutan = value.user.split(",");

					tableData += '<tr>';
					tableData += '<td>'+ index++ +'</td>';
					tableData += '<td>'+ value.judul +'</td>';

					tableData += '<td style=" text-align: left">';
					tableData += '<ol>';

					for(var i = 0; i < urutan.length; i++){
						tableData += '<li style="color: #e53935;">';
						tableData += '<a target="_blank" style="color: red;">';
						tableData += urutan[i];
						tableData += '</a>';
						tableData += '</li>';
					};

					tableData += '</ol>';
					tableData += '</td>';

					tableData += '<td style=" text-align: center;">';
					tableData += '<a onclick="ModalEdit(\''+value.judul+'\', \''+value.created_by+'\')" class="btn btn-success btn"  data-toggle="tooltip" title="Send Mail" style="width: 100px"><i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp';
					tableData += '<a onclick="DeleteKategori(\''+value.judul+'\', \''+value.created_by+'\')" class="btn btn-danger btn"  data-toggle="tooltip" title="Delete" style="width: 100px"><i class="fa fa-trash"></i> Hapus</a>';
					tableData += '</td>';
					tableData += '</tr>';

				});
				$('#bodyTableListApproval').append(tableData);

				var table = $('#TableListApproval').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[{
							extend: 'pageLength',
							className: 'btn btn-default',
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
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
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ModalEdit(judul, created_by){
		$('#modalEditShow').modal('show');
		$('#judul_kategori').html(judul);
		$('#jd_kategori').val(judul);
		var data = {
			judul:judul
		}
		$.get('{{ url("fetch/kategori/approval") }}',data, function(result, status, xhr){
			if(result.status){
				$('#TableDetail').DataTable().clear();
				$('#TableDetail').DataTable().destroy();
				$('#BodyTableDetail').html("");
				var tableData = "";
				var index = 1;
				$.each(result.list, function(key, value) {

					var urutan = value.user.split(",");
					var q = value.urutan;
					var w = result.list.length;

					tableData += '<tr>';
					tableData += '<td width="1%">'+ index++ +'</td>';
					tableData += '<td width="5%">'+ value.user +'</td>';
					tableData += '<td style=" text-align: center;" width="2%">';
					tableData += '<a onclick="DeleteList(\''+value.id+'\', \''+judul+'\')" class="btn btn-danger btn pull-right"  data-toggle="tooltip" title="Delete" style="width: 50px;"><i class="fa fa-trash"></i></a>';
					if (q == 1) {
						tableData += '&nbsp&nbsp';
						tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+judul+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
					}
					else if(q == w){
						tableData += '&nbsp&nbsp';
						tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+judul+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
					}
					else{
						tableData += '&nbsp&nbsp';
						tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+judul+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
						tableData += '&nbsp&nbsp';
						tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+judul+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
					}
					tableData += '</td>';
					tableData += '</tr>';

				});
				$('#BodyTableDetail').append(tableData);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function DeleteKategori(judul, created_by){
		if(confirm("Apakah anda yakin akan menghapus kategori approval ini?")){
			var jenis = 'Hapus Kategori';
			var data = {
				jenis:jenis,
				judul:judul,
				created_by:created_by
			}
			$.post('{{ url("delete/kategori/approval") }}', data, function(result, status, xhr) {
				if(result.status){
					openSuccessGritter('Success','Kategori Berhasil Di Hapus!');
					DataList();
				}else{
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			return false;
		}
	}

	function DeleteList(id, judul){
		if(confirm("Apakah anda yakin akan menghapus list kategori approval ini?")){
			var jenis = 'Hapus List';
			var data = {
				jenis:jenis,
				id:id,
				judul:judul
			}
			$.post('{{ url("delete/kategori/approval") }}', data, function(result, status, xhr) {
				if(result.status){
					openSuccessGritter('Success','List Kategori Approval Berhasil Di Hapus!');
					ModalEdit(judul);
					DataList();
				}else{
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			return false;
		}
	}

	function TambahApprover(){
		$("#modal_report").show();
		$("#modal_report").html('<div class="col-xs-9" style="padding-left: 0"><select class="form-control select2" id="add_user" name="add_user" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}/{{$row->name}}/{{$row->position}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select></div><div class="col-xs-3" style="padding-left: 0"><select onchange="AddApprover()" class="form-control select2" id="add_header" name="add_header" data-placeholder="Pilih Header" style="width: 100%" required><option value="">&nbsp;</option><option value="Created by/(作られた)">Created by</option><option value="Checked by/(チェック済み)">Checked by</option><option value="Approved by/(承認)">Approved by</option><option value="Known by/(承知)">Known by</option><option value="Prepared by/(準備)">Prepared by</option><option value="Received by/(が受信した)">Received by</option></select></div>');

		$('.select2').select2({
			dropdownParent: $('#modalEditShow'),
			allowClear : true,
		});
	}

	function AddApprover(){
		var judul = $('#judul_kategori').html();
		var user = $('#add_user').val();
		var header = $('#add_header').val();
		if (header == '') {
			return false;
		}else{
			var data = {
				judul:judul,
				user:user,
				header:header
			}
			$.post('{{ url("add/inject/approval") }}', data, function(result, status, xhr) {
				if(result.status){
					openSuccessGritter('Success','List Kategori Approval Berhasil Di Tambahkan!');
					ModalEdit(judul);
					DataList();
					$("#add_user").val('').trigger('change');
					$("#add_header").val('').trigger('change');
					$("#modal_report").hide();
				}else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function Naikkan(id, judul){
		var jenis = 'Naikkan';
		var data = {
			jenis:jenis,
			judul:judul,
			id:id
		}
		$.post('{{ url("pindah/posisi/approval") }}', data, function(result, status, xhr) {
			ModalEdit(judul);
			DataList();
		});
	}

	function Turunkan(id, judul){
		var jenis = 'Turunkan';
		var data = {
			jenis:jenis,
			judul:judul,
			id:id
		}
		$.post('{{ url("pindah/posisi/approval") }}', data, function(result, status, xhr) {
			ModalEdit(judul);
			DataList();
		});
	}

	function SimpanJudul(){
		var judul_before = $('#judul_kategori').html();
		var judul_after = $('#jd_kategori').val();
		var data = {
			judul_before:judul_before,
			judul_after:judul_after
		}
		$.post('{{ url("simpan/judul") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success','Judul Berhasil Diperbarui!');
				$('#jd_kategori').val(judul_after);
				ModalEdit(judul_after);
				DataList();
			}else{
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