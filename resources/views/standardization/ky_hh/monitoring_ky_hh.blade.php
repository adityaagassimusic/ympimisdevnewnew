@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
	#loading { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-2 pull-right" style="padding-bottom: 10px;">
				<center>
					<label style="color: white">Pilih FY</label>
				</center>
				<select class="form-control select2" name="fiscal_year" id='fiscal_year' style="width: 100%;" onchange="fillChart(this.value)">
					<!-- <option value="">&nbsp;</option> -->
					@foreach($fy as $fy)
					<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3" style="padding-right: 0;">
				<a href="{{ url('index/emp_data') }}">
					<!-- <div class="small-box" style="background: #E5DACE; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kiken Yochi</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: #0d47a1;"><b>誘導トレーニング</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="ky_bulan_sekarang"></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_ky_bulan_sekarang">0</h5>
						</div>
						<div class="icon" style="padding-top: 10px; font-size:10vh">
							<i class="fa fa-users" aria-hidden="true"></i>
						</div>
					</div> -->
				</a>
				<div class="small-box" style="background: #EBC8B4; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="showDetailKY('open_now')">
					<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kiken Yochi</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;" id="month"><b></b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="ky_bulan_sekarang"></h3>
						<h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red;" id="kikenyochi_open">0</h5>
					</div>
					<div class="icon" style="padding-top: 10px; font-size:5vh">
						<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
					</div>
				</div>
				<div class="small-box" style="background: #6CBF84; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="showDetailKY('all')">
					<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kiken Yochi</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Total Tim</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="ky_bulan_sekarang"></h3>
						<h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="kikenyochi_close">0</h5>
					</div>
					<div class="icon" style="padding-top: 10px; font-size:5vh">
						<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
					</div>
				</div>
			</div>
			<div class="col-xs-9" style="margin-top: 10px;padding-right: 5px">
				<div id="grafik02" style="width: 100%;height: 370px;"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-3" style="padding-right: 0;">
				<a href="{{ url('index/emp_data') }}">
					<!-- <div class="small-box" style="background: #E5DACE; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black">
						<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Kiken Yochi</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: #0d47a1;"><b>誘導トレーニング</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="ky_bulan_sekarang"></h3>
							<h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="total_ky_bulan_sekarang">0</h5>
						</div>
						<div class="icon" style="padding-top: 10px; font-size:5vh">
							<i class="fa fa-users" aria-hidden="true"></i>
						</div>
					</div> -->
				</a>
				<div class="small-box" style="background: #EBC8B4; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailEmpPutus()">
					<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Hiyari Hatto</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Open</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="hh_bulan_sekarang"></h3>
						<h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: red" id="hiyarihatto_open">0</h5>
					</div>
					<div class="icon" style="padding-top: 10px; font-size:5vh">
						<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
					</div>
				</div>
				<div class="small-box" style="background: #6CBF84; height: 20vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="DetailEmpPutus()">
					<div class="inner" style="padding-bottom: 0px;padding-top: 10px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Hiyari Hatto</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>Close</b></h3>
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: red;" id="hh_bulan_sekarang"></h3>
						<h5 style="font-size: 4vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="hiyarihatto_close">0</h5>
					</div>
					<div class="icon" style="padding-top: 10px; font-size:5vh">
						<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
					</div>
				</div>
			</div>
			<div class="col-xs-7" style="margin-top: 10px;padding-right: 5px">
				<div id="grafik01" style="width: 100%;height: 370px;"></div>
			</div>
			<div class="col-xs-2" style="margin-top: 10px;padding-right: 5px">
				<div class="row">
					<div class="col-xs-12">
						<div class="small-box" style="background: #f5ab17; height: 13vh; margin-bottom: 5px;cursor: pointer; color:black">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Significant</b></h3>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Open</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="tinggi">2</h3>
							</div>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Close</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="tinggi">0</h3>
							</div>
						</div>

						<div class="small-box" style="background: #f6bd4c; height: 13vh; margin-bottom: 5px;cursor: pointer; color:black">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Moderately Significant</b></h3>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Open</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="sedang">8</h3>
							</div>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Close</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="sedang">2</h3>
							</div>
						</div>

						<div class="small-box" style="background: #f2cd82; height: 13vh; margin-bottom: 5px;cursor: pointer; color:black">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>InSignificant</b></h3>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Open</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="sedang">33</h3>
							</div>
							<div class="col-xs-6">
								<h3 style="margin-bottom: 0px;font-size: 1vw;color: black; text-align: center"><b>Close</b></h3>
								<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: center" id="sedang">4</h3>
							</div>
						</div>
						<!-- <div class="small-box" style="background: #f6bd4c; height: 12vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="showDetailKY('open_now')">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;color: black;"><b>Moderately Significant</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: right; padding-right: 20px" id="sedang">0</h3>
						</div>
						<div class="small-box" style="background: #f2cd82; height: 12vh; margin-bottom: 5px;cursor: pointer; color:black" onclick="showDetailKY('open_now')">
							<h3 style="margin-bottom: 0px;font-size: 2vw;color: black;"><b>InSignificant</b></h3>
							<h3 style="margin-bottom: 0px;font-size: 3vw;color: red; text-align: right; padding-right: 20px" id="rendah">0</h3>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetailKY" data-backdrop="static" data-keyboard="false" style="z-index:10000">
	<div class="modal-dialog modal-lg" style="z-index:10000">
		<div class="modal-content" style="z-index:10000">
			<div class="modal-header"style="z-index:10000">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Detail Pengerjaan KY
					</h3>
				</center>
				<table class="table table-hover table-bordered table-striped" id="tableDetailTimKY">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th>No</th>
							<th>Nama Tim</th>
							<th>Nama Ketua Tim</th>
							<th>Bagian</th>
						</tr>
					</thead>
					<tbody id="bodytableBodyDetailTimKY" style="background-color: RGB(252, 248, 227);">
					</tbody>
				</table>
				<button type="button" class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1vw; width: 30%;">Kembali</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailHH" data-backdrop="static" data-keyboard="false" style="z-index:10000">
	<div class="modal-dialog modal-lg" style="z-index:10000">
		<div class="modal-content" style="z-index:10000">
			<div class="modal-header"style="z-index:10000">
				<center>
					<div id="header"></div>			
				</center>
				<table class="table table-hover table-bordered table-striped" id="tableDetailHH">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th>No</th>
							<th>Nama Karyawan</th>
							<th>Lokasi Temuan</th>
							<th>Tanggal Temuan</th>
							<th>Ringkasan Kejadian</th>
							<th>Level Temuan</th>
							<th>Penanganan</th>
						</tr>
					</thead>
					<tbody id="bodytableBodyDetailHH" style="background-color: RGB(252, 248, 227);">
					</tbody>
				</table>
				<button type="button" class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1vw; width: 30%;">Kembali</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPenanganan" data-backdrop="static" data-keyboard="false" style="z-index:10000">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Detail Temuan Hiyari Hatto</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-5">
							<div class="col-md-12">
								<label for="lokasi">Lokasi</label>
								: <br><span name="lokasi" id="lokasi"> </span>
							</div>
							<div class="col-md-12">
								<label for="tanggal">Tanggal</label>
								: <br><span name="tanggal" id="tanggal"> </span>
							</div>
							<div class="col-md-12">
								<label for="note">Kategori Temuan</label>
								: <br><span name="note" id="note"> </span>
							</div>
							<div class="col-md-12">
								<label for="image">Temuan</label>
								: <br><div name="temuan" id="temuan"></div>
							</div>
						</div>
						<div class="col-md-7">
							<h4>Catatan Penanganan</h4>
							<textarea class="form-control" required="" name="penanganan" id="penanganan" style="height: 100px;"></textarea> 
							<textarea class="form-control" required="" name="detail_penanganan" id="detail_penanganan" style="height: 100px;" readonly></textarea> 
							<h4>Bukti Penanganan</h4>
							<div class="col-sm-12" style="padding-top: 10px;">
								<center>
									<img id="image-preview" style="width: 400px">
								</center>
							</div><br>
							<div id="gambar_penanganan"></div>
							<!-- <input type="file" required="" id="bukti_penanganan" name="bukti_penanganan" accept="image/*" capture="environment"> -->
							<input type="file" name="file_gambar" id="file_gambar" onchange="previewImage()" accept="image/*" required=""><br>
							<span id="ket">Klik (Close Temuan) untuk menyelesaikan penanganan</span><br>
							<!-- <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_progress" onclick="btnAction('progress')" class="btn btn-md">Progress</a> -->
							<a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_close" onclick="btnAction('close')" class="btn btn-md">Close Temuan</a>
							<input type="hidden" id="btn_status" name="btn_status" required="">
						</div>

						<div class="col-md-12" id="status_progress" style="display: none;">
							<div class="col-xs-12">
								<div class="row">
									<hr style="border: 1px solid red;background-color: red">
								</div>
							</div>
							<div class="col-md-12">
								<label for="note_progress">Penanganan Progress</label>
								: <span name="note_progress" id="note_progress"> </span>
							</div>
							<div class="col-md-12">
								<label for="images_progress">Foto Progress</label>
								: <div name="images_progress" id="images_progress"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
				<input type="hidden" id="id_penanganan">
				<button type="button" onclick="update_penanganan()" class="btn btn-success"><i class="fa fa-pencil"></i> Submit Penanganan</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script> -->
<!-- <script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function(){
		$('body').toggleClass("sidebar-collapse");
		fillChart();
		$('.select2').select2({
			allowClear : true,
		});
	});

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

	function previewImage() {
		document.getElementById("image-preview").style.display = "block";
		var oFReader = new FileReader();
		oFReader.readAsDataURL(document.getElementById("file_gambar").files[0]);
		oFReader.onload = function(oFREvent) {
			document.getElementById("image-preview").src = oFREvent.target.result;
		};
	};

	function update_penanganan() {

		if ($("#penanganan").val() == "") {
			openErrorGritter("Error","Catatan Penanganan Harus Diisi");
			return false;
		}

		if ($("#file_gambar").val() == "") {
			openErrorGritter("Error","Bukti Penanganan Harus Diisi");
			return false;
		}

		if ($("#btn_status").val() == "") {
			openErrorGritter("Error","Status Penanganan Harus Diisi");
			return false;
		}


		var formData = new FormData();
		formData.append('id', $("#id_penanganan").val());
		formData.append('penanganan', $("#penanganan").val());
		formData.append('file_gambar', $('#file_gambar').prop('files')[0]);
		formData.append('btn_status', $("#btn_status").val());

		$.ajax({
			url:"{{ url('update/penanganan/hiyarihatto') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				openSuccessGritter("Success","Temuan Hiyarihatto Berhasil Ditangani.");
				$('#modalPenanganan').modal("hide");
				location.reload();
				$('#penanganan').val('');
				$('#file_gambar').val('');
			},
			error: function (response) {
				openErrorGritter("Error", 'Lakukan Pengisian Dengan Benar.');
        // $('#modalPenanganan').modal("hide");
			},
		});

	}

	function showDetailKY(periode, qq, ket){
		var data = {
			ket:ket,
			periode:periode
		}
		$.get('{{ url("fetch/detail/tim") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modalDetailKY").modal('show');
				$('#tableDetailTimKY').DataTable().clear();
				$('#tableDetailTimKY').DataTable().destroy();
				$('#bodytableBodyDetailTimKY').html("");
				var tableData = "";
				var index = 1;

				$.each(result.data, function(key, value){

					tableData += '<tr>';
					tableData += '<td>'+ index++ +'</td>';
					tableData += '<td>'+value.nama_tim+'</td>';
					tableData += '<td>'+value.nama+'</td>';
					tableData += '<td>'+value.department+'</td>';
					tableData += '</tr>';

				});
				$('#bodytableBodyDetailTimKY').append(tableData);

				var table = $('#tableDetailTimKY').DataTable({
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

	function showDetailHH(bulan, ket){
		if (ket == 'Open') {
				// <h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
				// 		Detail Temuan Hiyari Hatto <br> OPEN
				// 	</h3>
			$('#header').html('<h3 style="background-color: red; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Detail Temuan Hiyari Hatto <br> OPEN</h3>');
		}else{
			$('#header').html('<h3 style="background-color: green; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Detail Temuan Hiyari Hatto <br> Close</h3>');
		}

		var data = {
			ket:ket,
			bulan:bulan
		}
		$.get('{{ url("fetch/detail/hiyarihatto") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modalDetailHH").modal('show');
				$('#tableDetailHH').DataTable().clear();
				$('#tableDetailHH').DataTable().destroy();
				$('#bodytableBodyDetailHH').html("");
				var tableData = "";
				var index = 1;

				$.each(result.data, function(key, value){

					var op = value.karyawan;
					var emp = op.split('/');

					var ringkasan = value.ringkasan;
					var ring = ringkasan.split('/');

					var level = value.level;
					var lv = level.split('/');

					tableData += '<tr>';
					tableData += '<td>'+ index++ +'</td>';
					tableData += '<td>'+emp[1]+'<br>('+emp[0]+')<br>'+emp[3]+'</td>';
					tableData += '<td>'+value.lokasi+'</td>';
					tableData += '<td style="text-align: center">'+value.tanggal+'</td>';
					tableData += '<td>'+ring[0]+'</td>';
					tableData += '<td style="text-align: center">'+lv[0]+' ('+lv[1]+')<br>'+lv[2]+'</td>';
					if (value.remark == 'Open') {
						tableData += '<td style="text-align: center"><button style="height: 100%;" onclick="Penanganan(\''+value.id+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-thumbs-o-up"></i> Penanganan</button></td>';
					}else{
						tableData += '<td style="text-align: center"><button style="height: 100%;" onclick="DetailPenanganan(\''+value.id+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-thumbs-o-up"></i> Penanganan</button></td>';
					}
					tableData += '</tr>';

				});
				$('#bodytableBodyDetailHH').append(tableData);

				var table = $('#tableDetailHH').DataTable({
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


	function Penanganan(id){
		$("#modalDetailHH").modal('hide');

		var data = {
			id:id
		}
		$.get('{{ url("get/detail/data") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modalPenanganan").modal('show');
				$("#lokasi").html(result.data.lokasi);
				$("#tanggal").html(result.data.tanggal);
				$("#id_penanganan").val(result.data.request_id);
				$("#detail_penanganan").hide();
				$("#penanganan").show();
				$("#file_gambar").show();
				$("#btn_close").show();
				$("#ket").show();
				$("#footer").show();
				$("#gambar_penanganan").hide();


				var level = result.data.level;
				var lv = level.split('/');
				$("#note").html(lv[2]);

				var note = result.data.ringkasan;
				var nt = note.split('/');
				$("#temuan").html(nt[0]);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function DetailPenanganan(id){
		$("#modalDetailHH").modal('hide');

		var data = {
			id:id
		}
		$.get('{{ url("get/detail/data") }}', data, function(result, status, xhr){
			if(result.status){
				var url = "{{ url('data_file/std/ky') }}/"+result.data.bukti_penanganan;
				$("#modalPenanganan").modal('show');
				$("#lokasi").html(result.data.lokasi);
				$("#tanggal").html(result.data.tanggal);
				$("#id_penanganan").val(result.data.request_id);
				$("#penanganan").hide();
				$("#detail_penanganan").show().val(result.data.penanganan);
				$("#file_gambar").hide();
				$("#btn_close").hide();
				$("#ket").hide();
				$("#footer").hide();
				$("#gambar_penanganan").html('<img src="'+url+'" style="width: 85%">');


				var level = result.data.level;
				var lv = level.split('/');
				$("#note").html(lv[2]);

				var note = result.data.ringkasan;
				var nt = note.split('/');
				$("#temuan").html(nt[0]);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function btnAction(cat){

		$('#btn_progress').css('background-color', 'white');
		$('#btn_close').css('background-color', 'white');

		if (cat == "close") {
			$('#btn_'+cat).css('background-color', '#90ed7d');
		}else{
			$('#btn_'+cat).css('background-color', '#f39c12');
		}
		$('#btn_status').val(cat);
	}

	function fillChart() {
		$("#loading").show();
		var fiscal_year = $('#fiscal_year').val();

		// var dept = $('#select_dept').val();
		// var select_bulan = $('#select_month').val();
		var data = {
			// dept:dept,
			// select_bulan:select_bulan
			fiscal_year:fiscal_year
		}

		$.get('{{ url("fetch/monitoring/ky_hh") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$("#loading").hide();
					var categori = [];
					var series = [];
					var series_open = [];
					var series_close = [];


					var categori_ky = [];
					var series_ky = [];
					var series_open_ky = [];
					var series_close_ky = [];

					var jml_sudah = [];
					var jml_belum = [];

					$.each(result.wc, function(key, value){
						var isi = 0;
						var isi2 = 0;
						var date = new Date(value.bulan);
						var nama_bulan = date.getMonth();
						switch(nama_bulan) {
						case 0: nama_bulan = "January"; break;
						case 1: nama_bulan = "February"; break;
						case 2: nama_bulan = "March"; break;
						case 3: nama_bulan = "April"; break;
						case 4: nama_bulan = "May"; break;
						case 5: nama_bulan = "June"; break;
						case 6: nama_bulan = "July"; break;
						case 7: nama_bulan = "August"; break;
						case 8: nama_bulan = "September"; break;
						case 9: nama_bulan = "October"; break;
						case 10: nama_bulan = "November"; break;
						case 11: nama_bulan = "December"; break;
						}

						categori.push(nama_bulan);

						$.each(result.data, function(key2, value2){
							if (value.bulan == value2.periode) {
								series.push({y:parseInt(value2.jumlah), key: value.bulan});
								series_open.push({y:parseInt(value2.open), key: value.bulan, key2: 'Open'});
								series_close.push({y:parseInt(value2.close), key: value.bulan, key2: 'Close'});
								isi = 1;
							}
						});

						if (isi == 0) {
							series.push(0);
							series_open.push(0);
							series_close.push(0);
						}

						$.each(result.ky_sudah, function(key2, value2){
							if (value.bulan == value2.periode) {
								series_ky.push({y:parseInt(value2.jumlah), key: value.bulan});
								series_close_ky.push({y:(value2.jml), key: value.bulan, key2: 'Close'});
								series_open_ky.push({y:(result.jumlah_tim_ky - value2.jml), key: value.bulan, key2: 'Open'});
								jml_sudah.push(value2.jml);
								isi2 = 1;
							}
						});
						if (isi2 == 0) {
							series_ky.push(0);
							series_close_ky.push(0);
							series_open_ky.push(0);
						}







						// $.each(result.ky_belum, function(key2, value2){
						// 	if (value.bulan == value2.periode) {
						// 		series_ky.push({y:parseInt(value2.jumlah), key: value.bulan});
						// 		series_open_ky.push({y:(result.jumlah_tim_ky - ), key: value.bulan, key2: 'Open'});
						// 		jml_belum.push(value2.jml);
						// 		isi2 = 1;

						// 		series.push({y:parseInt(value2.jumlah), key: value.bulan});
						// 	}
						// });
						// if (isi2 == 0) {
						// 	series_ky.push(0);
						// 	series_open_ky.push(0);
						// }
					});

					$('#hiyarihatto_open').html(result.hh_open[0].jumlah + ' Temuan');
					$('#hiyarihatto_close').html(result.hh_close[0].jumlah + ' Temuan');
					$('#kikenyochi_close').html(result.jumlah_tim_ky);
					$('#kikenyochi_open').html(result.tim_open_now);
					$('#month').html(result.month_now);

					Highcharts.chart('grafik01', {
						chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "Monitoring Temuan Hiyarihato",
							style: {
								fontSize: '30px'
							}
						},
						xAxis: {
							categories: categori,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '16px',
									fontWeight: 'bold'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Jumlah MP',
								style: {
									color: '#eee',
									fontSize: '18px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"14px"
								}
							},
							type: 'linear',
						},
						legend: {
							enabled:true,
							reversed : true
						},	
						credits: {
							enabled: false
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {
											showDetailHH(this.options.key, this.options.key2);
										}
									}
								},
								dataLabels: {
									enabled: true,
									style:{
										fontSize: '1vw'
									},
									formatter: function(){
										return (this.y!=0)?this.y:"";
									}
								},
								animation: {
									enabled: true,
									duration: 800
								},
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								stacking: 'normal'
							},
						},
						series: [
						// {
						// 	type: 'column',
						// 	data: series,
						// 	name: "Jumlah",
						// 	colorByPoint: false,
						// 	color: "#aa6eff",
						// 	animation: false,
						// 	stack:'GF'
						// },
						{
							type: 'column',
							data: series_open,
							name: "Open",
							colorByPoint: false,
							color: "red",
							animation: false,
							stack:'GG'
						},{
							type: 'column',
							data: series_close,
							name: "Close",
							colorByPoint: false,
							color: "green",
							animation: false,
							stack:'GG'
						}]
					});	

					Highcharts.chart('grafik02', {
						chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "Monitoring Pelaksanaan Kiken Yochi",
							style: {
								fontSize: '30px'
							}
						},
						xAxis: {
							categories: categori,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '16px',
									fontWeight: 'bold'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Jumlah MP',
								style: {
									color: '#eee',
									fontSize: '18px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"14px"
								}
							},
							type: 'linear',
						},
						legend: {
							enabled:true,
							reversed : true
						},	
						credits: {
							enabled: false
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {
											showDetailKY(this.options.key, this.category, this.options.key2);
										}
									}
								},
								dataLabels: {
									enabled: true,
									style:{
										fontSize: '1vw'
									},
									formatter: function(){
										return (this.y!=0)?this.y:"";
									}
								},
								animation: {
									enabled: true,
									duration: 800
								},
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								stacking: 'normal'
							},
						},
						series: [
						// {
						// 	type: 'column',
						// 	data: series,
						// 	name: "Jumlah",
						// 	colorByPoint: false,
						// 	color: "#aa6eff",
						// 	animation: false,
						// 	stack:'GF'
						// },
						{
							type: 'column',
							data: series_open_ky,
							name: "Open",
							colorByPoint: false,
							color: "red",
							animation: false,
							stack:'GG'
						},{
							type: 'column',
							data: series_close_ky,
							name: "Close",
							colorByPoint: false,
							color: "green",
							animation: false,
							stack:'GG'
						}]
					});	
				}
			}
		});
}

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);

</script>
@endsection