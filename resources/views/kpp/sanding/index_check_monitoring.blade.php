
@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
		overflow:hidden;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:2px;
	}

	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	input[type=radio]{
		width: 18px;
		height: 18px;
	}

	table {
		margin-bottom: 10px !important;
	}

	.radio_text {
		font-weight: bold;
		font-size: 20px;
		margin-right: 5px;
	}

	hr {
		border-color: black;
		margin-top: 3px;
		margin-bottom: 3px;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		<div class="col-xs-12 col-md-9 col-lg-9">
			<h3 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
		</div>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-2" style="padding-bottom: 8px">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="mon" placeholder="Pilih Bulan" onchange="loadData()">
			</div>
		</div>
		<!-- <div class="col-xs-2">
			<button class="btn btn-primary" onclick="kirim_email()">send email</button>
		</div> -->
		
		<div class="col-xs-12">
			<div id="chart"></div>
		</div>
		<div class="col-xs-2" style="padding-bottom: 8px; margin-bottom: 10px; margin-top: 10px">
			<label style="color: white">Kategori : </label>
			<select class="form-control select2" id="category" data-placeholder="Pilih Kategori">
				<option value=""></option>
				<option value="Open" selected>Open</option>
				<option value="Close">Close</option>
				<option value="All">All</option>
			</select>
		</div>
		<div class="col-xs-2" style="margin-top: 10px; margin-bottom: 10px">
			<br>
			<button class="btn btn-primary" onclick="loadData()"><i class="fa fa-filter"></i> Filter</button>
		</div>
		<div class="col-xs-12">
			<table class="table table-bordered" id="tableFU">
				<thead style="background-color: #a488aa; color: black">
					<tr>
						<th style="width: 1%">No</th>
						<th style="width: 2%">Material Number</th>
						<th style="width: 20%">Material Description</th>
						<th>Point Description</th>
						<th>Kualitas</th>
						<th style="width: 15%">Tanggal Temuan</th>
						<th style="width: 5%">Status</th>
						<th style="width: 5%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyFU" style="background-color: white"></tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="modalFU">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<center><div style="font-weight: bold; font-size: 20px" id="judul_modal_FU"></div></center><br>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label>1. Isi bukti periodik molding dengan melampirkan screenshot :<span class="text-red">*</span></label>
								<input type="hidden" id="mat_num">
								<input type="hidden" id="mat_desc">
								<input type="file" id="periodik_img" accept="image/*" class="file">
							</div>

							<div class="form-group">
								<label>2. Upload foto material OK :<span class="text-red">*</span></label>
								<input type="file" id="material_img" accept="image/*" class="file">
							</div>

							<div class="form-group" id="div_catatan" style="display: none">
								<label>3. Catatan :</label><br>
								<textarea cols="form-control" id="note2" style="width: 100%" readonly disabled></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="upload_btn()"><i class="fa fa-upload"></i> Upload</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalConf">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<center><div style="font-weight: bold; font-size: 20px" id="judul_modal_Conf"></div></center><br>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label>1. Isi bukti periodik molding dengan melampirkan screenshot :<span class="text-red">*</span></label>
								<input type="hidden" id="mat_num_app">
								<input type="hidden" id="mat_desc_app">
								<input type="hidden" id="form_number">
								<img src="#" id="detail_periodik" style="max-width: 100%">
							</div>

							<div class="form-group">
								<label>2. Upload foto material OK :<span class="text-red">*</span></label>
								<img src="#" id="detail_material" style="max-width: 100%">
							</div>

							<div class="form-group">
								<label>3. Catatan :</label><br>
								<textarea cols="form-control" id="note" placeholder="Masukkan Catatan" style="width: 100%"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" onclick="approve('Rejected')" style="width: 49%"><i class="fa fa-close"></i> Reject</button>
					<button class="btn btn-success pull-right" onclick="approve('Approved')" style="width: 49%"><i class="fa fa-check"></i> Approve</button>
				</div>
			</div>
		</div>
	</div>
	

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<center><div style="font-weight: bold; font-size: 20px" id="judul_modal"></div></center>
					<table class="table table-bordered" id="tableDetail">
						<thead style="background-color: #a488aa">
							<tr>
								<th style="width: 1%">No</th>
								<th style="width: 2%">Material Number</th>
								<th style="width: 20%">Material Description</th>
								<!-- <th style="width: 2%">Point</th> -->
								<th>Point Description</th>
								<th style="width: 20%">Check Date</th>
							</tr>
						</thead>
						<tbody id="bodyDetail" style="background-color: white"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</section>
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

<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var detail = [];
	var temuan = [];
	var role = "{{ Auth::user()->role_code }}";

	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");
		loadData();

	});	

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		todayHighlight: true,
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});

	$('.datepicker2').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,
	});

	$('.select2').select2();	

	function loadData() {
		$("#loading").show();

		var data = {
			date : $("#mon").val(),
			category : $("#category").val()
		}

		var series_3 = [];
		var series_4 = [];
		var series_5 = [];

		var series_3_done = [];
		var series_4_done = [];
		var series_5_done = [];

		var categories = [];

		$.get('{{ url("fetch/monitoring/material_check/sanding") }}', data, function(result, status, xhr){
			$("#loading").hide();
			detail = result.detail_cek;
			temuan = result.temuan_lists;
			var tiga_belum = 0;
			var tiga_sudah = 0;
			var empat_belum = 0;
			var empat_sudah = 0;
			var lima_belum = 0;
			var lima_sudah = 0;
			
			$.each(result.data_cek, function(key, value) {
				series_3.push(parseInt(value.tiga_belum));
				series_3_done.push(parseInt(value.tiga_sudah));

				series_4.push(parseInt(value.empat_belum));
				series_4_done.push(parseInt(value.empat_sudah));

				series_5.push(parseInt(value.lima_belum));
				series_5_done.push(parseInt(value.lima_sudah));

				if(categories.indexOf(value.hari) === -1){
					categories[categories.length] = value.hari;
				}

			});

			// console.log(series_4);

			Highcharts.chart('chart', {
				chart: {
					type: 'column',
					options3d: {
						enabled: true,
						alpha: 15,
						beta: 0,
						viewDistance: 50,
						depth: 50
					}
				},

				title: {
					text: 'Monitoring Cek Material Awal Kualitas Buruk'
				},

				credits: {
					enabled: false
				},

				xAxis: {
					categories: categories,
					labels: {
						skew3d: true,
						style: {
							fontSize: '12px'
						}
					}
				},

				yAxis: {
					allowDecimals: false,
					min: 0,
					title: {
						text: 'Jumlah Material',
						skew3d: true
					},
					labels:{
						style:{
							fontSize:"12px"
						}
					},
					stackLabels: {
						enabled: true,
						align: 'center',
						style:{
							fontSize:"16px"
						}
					}
				},

				tooltip: {
					headerFormat: '<b>{point.key}</b><br>',
					pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y}'
				},

				plotOptions: {
					column: {
						stacking: 'normal',
						depth: 40
					}, series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									detail_cek(this.category, this.series.name);
								}
							}
						},
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								fontSize: '1.2vw'
							}
						}
					}
				},

				series: [{
					name: 'Level 3',
					data: series_3,
					color: '#d7cc51',
					stack: '3'
				}, {
					showInLegend: false,
					name: 'Sudah 3',
					data: series_3_done,
					color: '#56ff4a',
					stack: '3'
				}, {
					name: 'Level 4',
					data: series_4,
					color: '#d68851',
					stack: '4'
				}, {
					showInLegend: false,
					name: 'Sudah 4',
					data: series_4_done,
					color: '#56ff4a',
					stack: '4'
				}, {
					name: 'Level 5',
					data: series_5,
					color: '#cc403b',
					stack: '5'
				}, {
					showInLegend: false,
					name: 'Sudah 5',
					data: series_5_done,
					color: '#56ff4a',
					stack: '5'
				}]
			});

			var bodyFU = '';

			$("#bodyFU").empty();
			$.each(result.resume_temuan, function(key, value) {
				bodyFU += '<tr>';
				bodyFU += '<td>'+(key+1)+'</td>';
				bodyFU += '<td>'+value.material_number+'</td>';
				bodyFU += '<td>'+value.material_description+'</td>';
				bodyFU += '<td style="text-align: left">';
				var pnt_arr = value.pn.split('|');
				var pnt_arr2 = value.des.split('|');

				$.each(pnt_arr, function(key2, value2) {
					bodyFU += value2+' - '+pnt_arr2[key2]+'<br>';
				})

				bodyFU += '</td>';

				if (value.cp == '3') {
					bodyFU += '<td><b>Level 3</b> <br> Bentuk material cukup jelek (Sedikit ada perubahan bentuk, sedikit menambah waktu proses)</td>';
				} else if (value.cp == '4') {
					bodyFU += '<td><b>Level 4</b> <br> Bentuk material jelek (Ada perubahan bentuk, menambah waktu proses)</td>';
				} else if (value.cp == '5') {
					bodyFU += '<td><b>Level 5</b> <br> Bentuk material sangat jelek (Ada perubahan bentuk, menambah waktu proses)</td>';
				}

				bodyFU += '<td>'+value.check_time+'</td>';
				bodyFU += '<td>'+(value.stat || '')+'</td>';
				bodyFU += '<td>';

				if ("{{ strtoupper(Auth::user()->username) }}" == 'PI0704009' || "{{ strtoupper(Auth::user()->username) }}" == 'PI9811006' || "{{ strtoupper(Auth::user()->username) }}" == 'PI0005016' || ~role.indexOf("MIS")) {

					if (value.stat == 'Waiting') {
						bodyFU += '<button class="btn btn-primary" onclick="openConfModal(\''+value.material_number+'\', \''+value.material_description+'\', \''+value.form_number+'\')">Verifikasi</button>';
					} else if (value.stat == 'Approved'){
						
					} else {
						bodyFU += '<button class="btn btn-success" onclick="openFUModal(\''+value.material_number+'\', \''+value.material_description+'\', \''+value.stat+'\', \''+value.form_number+'\')">Follow Up</button>';
					}
				}


				bodyFU += '</td>';

				bodyFU += '</tr>';
			})

			$("#bodyFU").append(bodyFU);
		})
}

function detail_cek(cat, name) {
	var desc = '';
	var name2 = name.split(' ')[1];

	if (name2 == '3') {
		desc = '<span style="color: #f25438">\"Bentuk material cukup jelek (Sedikit ada perubahan bentuk, sedikit menambah waktu proses)\"</span>';
	} else if (name2 == '4') {
		desc = '<span style="color: #f25438">\"Bentuk material jelek (Ada perubahan bentuk, menambah waktu proses)\"</span>';
	} else if (name2 == '5') {
		desc = '<span style="color: #f25438">\"Bentuk material sangat jelek (Ada perubahan bentuk, menambah waktu proses)\"</span>';
	}

	$("#judul_modal").html('Point Pengecekan pada '+cat+' dengan Level Kualitas "'+name2+'" <br> '+desc); 
	$("#modalDetail").modal('show');
	$("#bodyDetail").empty();
	var body = "";
	var no = 1;

	var cek = [];

	$.each(detail, function(key, value) {
		if (value.check_point == name2 && value.dt == cat) {
			body += "<tr>";

			if(cek.indexOf(value.material_number) === -1){
				cek[cek.length] = value.material_number;
				body += "<td style='background-color: #efc5fc'>"+no+"</td>";
				body += "<td style='background-color: #efc5fc'>"+value.material_number+"</td>";
				body += "<td style='background-color: #efc5fc'>"+value.material_description+"</td>";
				no++;
			} else {
				body += "<td></td>";
				body += "<td></td>";
				body += "<td></td>";
			}

			var poin = value.point.split(',');
			var poin_desc = value.point_description.split(',');
			var pn = '';
			$.each(poin, function(key1, value1) {
				pn += poin[key1]+" - "+poin_desc[key1];

				if (key1 != (poin.length-1)) {
					pn += "<hr>";
				}
			})
			body += "<td>"+pn+"</td>";
			body += "<td>"+value.created_at+"</td>";
			body += "</tr>";
		}
	})

	$("#bodyDetail").append(body);
}

function openFUModal(gmc, desc, stat, form_number) {
	$("#modalFU").modal('show');
	$("#judul_modal_FU").text("Follow Up "+gmc+" - "+desc);
	$("#mat_num").val(gmc);
	$("#mat_desc").val(desc);

	if (stat == 'Rejected') {
		$("#div_catatan").show();

		$.each(temuan, function(key, value) {
			if (form_number == value.form_number) {
				$("#note2").val(value.note);
			}
		})

	} else {
		$("#div_catatan").hide();
		$("#note2").val('');
	}
}

function openConfModal(gmc, desc, form_number) {
	$("#modalConf").modal('show');

	$("#judul_modal_Conf").html("Report Follow Up <br> "+gmc+" - "+desc);

	$("#mat_num_app").val(gmc);
	$("#mat_desc_app").val(desc);

	$.each(temuan, function(key, value) {
		if (form_number == value.form_number) {
			$("#detail_periodik").attr('src', '{{ url("sanding/visual_check/FU") }}/'+value.molding_evidence);
			$("#detail_material").attr('src', '{{ url("sanding/visual_check/FU") }}/'+value.material_evidence);
			$("#form_number").val(form_number);
		}
	})

}

function upload_btn() {
	$("#loading").show();

	if( document.getElementById("periodik_img").files.length == 0 ){
		openErrorGritter('Error', 'Harap melengkapi screenshot periodik molding');
		$("#loading").hide();
		return false;
	}

	if( document.getElementById("material_img").files.length == 0 ){
		openErrorGritter('Error', 'Harap melengkapi foto material OK');
		$("#loading").hide();
		return false;
	}

	var formData = new FormData();
	formData.append('material_number', $("#mat_num").val());
	formData.append('material_description', $("#mat_desc").val());
	formData.append('ss_periodik', $('#periodik_img').prop('files')[0]);
	formData.append('foto_material', $('#material_img').prop('files')[0]);

	$.ajax({
		url: '{{ url("post/followup/material_check/sanding") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

			$("#periodik_img").val("");
			$("#material_img").val("");
			$("#mat_num").val("");
			$("#mat_desc").val("");
			$("#note2").val("");

			$('#modalFU').modal('hide');

			openSuccessGritter('Success', 'Upload Data Success');

			loadData();

		},
		error: function(result, status, xhr){
			$("#loading").hide();

			openErrorGritter('Error!', result.message);
		},
		cache: false,
		contentType: false,
		processData: false
	});
}

function approve(category) {
	$("#loading").show();

	if (category == 'Rejected' && $("#note").val() == '') {
		openErrorGritter('Perhatian', 'Mohon lengkapi Catatan');
		$("#loading").hide();
		return false;
	}

	data = {
		approval : category,
		form_number : $("#form_number").val(),
		material_number : $("#mat_num_app").val(),
		note : $("#note").val()
	}

	$.post('{{ url("approval/followup/material_check/sanding") }}',data, function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success', 'Approve Berhasil');
			window.setTimeout( location.reload(), 3000 );
			$("#note").val('');
		} else {
			$("#loading").hide();
			openErrorGritter('Error', result.message);
		}
	})
}

const compressImage = async (file, {quality = 1, maxWidth = 1200, maxHeight = 1000, type = file.type}) => {
	const imageBitmap = await createImageBitmap(file);

	let width = imageBitmap.width;
	let height = imageBitmap.height;
	if (width > maxWidth) {
		height *= maxWidth / width;
		width = maxWidth;
	}
	if (height > maxHeight) {
		width *= maxHeight / height;
		height = maxHeight;
	}

	const canvas = document.createElement('canvas');
	canvas.width = width;
	canvas.height = height;
	const ctx = canvas.getContext('2d');
	ctx.drawImage(imageBitmap, 0, 0, width, height);

	const blob = await new Promise((resolve) =>
		canvas.toBlob(resolve, type, quality)
		);

	return new File([blob], file.name, {
		type: blob.type,
	});
};


const input = document.querySelector('#periodik_img');
input.addEventListener('change', async (e) => {            
	const {
		files
	} = e.target;

	if (!files.length) return;

	const dataTransfer = new DataTransfer();

	for (const file of files) {

		if (!file.type.startsWith('image')) {                    
			dataTransfer.items.add(file);
			continue;
		}

		const compressedFile = await compressImage(file, {
			quality: 0.5
		});

		dataTransfer.items.add(compressedFile);
	}

	e.target.files = dataTransfer.files;
});

const input2 = document.querySelector('#material_img');
input2.addEventListener('change', async (e) => {
	const {
		files
	} = e.target;

	if (!files.length) return;

	const dataTransfer = new DataTransfer();

	for (const file of files) {

		if (!file.type.startsWith('image')) {                    
			dataTransfer.items.add(file);
			continue;
		}

		const compressedFile = await compressImage(file, {
			quality: 0.5
		});

		dataTransfer.items.add(compressedFile);
	}

	e.target.files = dataTransfer.files;
});

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});

	audio_ok.play();
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

	audio_error.play();
}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

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
				// textTransform: 'uppercase',
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