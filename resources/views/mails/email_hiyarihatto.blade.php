<style type="text/css">
	table.table-bordered {
		border: 1px solid black;
	}

	table.table-bordered>thead>tr>th {
		border: 1px solid black;
		vertical-align: middle;
		text-align: center;
	}

	table.table-bordered>tbody>tr>td {
		border: 1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
		text-align: center;
	}

	table.table-bordered>tfoot>tr>th {
		border: 1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label,
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	.nav-tabs-custom>ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}

	.nav-tabs-custom>ul.nav.nav-tabs>li {
		float: none;
		display: table-cell;
	}

	.nav-tabs-custom>ul.nav.nav-tabs>li>a {
		text-align: center;
	}

	#loading,
	#error {
		display: none;
	}
</style>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div>
		<center>
			<div style="width: 100%">
				<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
					<thead>
						<tr>
							<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
							<td style="font-weight: bold;font-size: 13px; text-align: right">No Dokumen : YMPI/STD/FK3/044</td>
						</tr>
						<tr>
							<td colspan="9" style="text-align: left;font-size: 13px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
							<td style="font-size: 13px; text-align: right">No Request : {{ $data[0]->request_id}}</td>
						</tr>
						<tr>
							<td colspan="9" style="text-align: left;font-size: 13px">Phone : (0343) 740290 Fax : (0343) 740291</td>
							<td style="font-size: 13px; text-align: right">Tanggal Dibuat : {{ $data[0]->created_at}}</td>
						</tr>
						<tr>
							<td colspan="10" style="text-align: left;font-size: 13px">Jawa Timur Indonesia</td>
						</tr>
					</thead>
				</table>
				<br>

				<?php
				$karyawan = explode('/', $data[0]->karyawan);
				$saksi = explode('/', $data[0]->saksi);
				$kejadian = explode('/', $data[0]->ringkasan);
				$detail = explode('/', $data[0]->detail);
				$level = explode('/', $data[0]->level);
				?>

				<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
					<thead>
						<tr>
							<td colspan="2" style="text-align: center; width: 50%"> Category Resiko Hiyari Hatto :</td>
						</tr>
						@if($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Tinggi')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white;">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Sedang')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Tinggi' && $kejadian[3] == 'Kemungkinan Rendah')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: red; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Tinggi')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: yellow; color: red">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Sedang')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: yellow; color: red">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Sedang' && $kejadian[3] == 'Kemungkinan Rendah')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Tinggi')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Sedang')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@elseif($kejadian[2] == 'Keparahan Rendah' && $kejadian[3] == 'Kemungkinan Rendah')
						<tr>
							<td colspan="2" style="text-align: center; width: 50%; background-color: green; color: white">{{ $level[0]}} <br> ({{ $level[1] }})</td>
						</tr>
						@endif
					</thead>
				</table>

				<br>

				<table class="table table-bordered table-hover" style="width: 100%; margin-bottom: 0px; text-align: center">
					<thead style="background-color: #BDD5EA; color: black;">
						<tr>
							<th colspan="2" style="font-size: 13px;">Kejadian berdasarkan pengalaman selama bekerja, yang hampir menimbulkan
								kecelakaan kerja yang kemudian dilakukan tindakan penanganan untuk menghilangkan-nya
							</th>
						</tr>
					</thead>
					<tbody style="background-color: #fcf8e3;">
						<tr>
							<td style="text-align: center; width: 100%; font-size: 13px" colspan="2">Nama Tim : {{ $data[0]->nama_tim}}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Pelapor</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $karyawan[0] }} - {{ $karyawan[1] }}<br>{{ $karyawan[3] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Saksi</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $saksi[0] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Tanggal Kejadian</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->tanggal }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Lokasi Kejadian</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->lokasi }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Ringkasan Kejadian</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $kejadian[0] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Alat Pelindung Diri yang digunakan (jika ada)</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $kejadian[1] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Keparahan</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $detail[0] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px">Kemungkinan</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $detail[1] }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px"> Tindakan Perbaikan - Tindakan yang dilakukan atau sudah dilakukan untuk mencegah kejadian tersebut tidak terulang lagi</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->perbaikan }}</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 50%; font-size: 13px"> Informasi Lain</td>
							<td style="text-align: center; width: 50%; font-size: 13px">{{ $data[0]->lain_lain}}</td>
						</tr>
					</tbody>
				</table>
				<br>

				<table style="width: 100%">
					<tr>
						<th style="width: 10%; font-weight: bold; color: black;">
							<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"  href="{{ url('index/penanganan/hiyarihatto/'.$data[0]->request_id.'/'.$data[0]->id_ketua) }}">&nbsp; Klik Untuk Penanganan Hiyari Hatto&nbsp;</a>
						</th>
					</tr>
				</table>
				<br>

				<table style="width: 100%; border-collapse: collapse; text-align: center;">
					<thead>
						<tr>
							<td colspan="10" style="font-weight: bold;font-size: 13px">---------- <?php
							echo "Update : " . date("Y-m-d h:i");
						?> ----------</td>
					</tr>
				</table>
			</div>
		</center>
	</div>
</body>
</html>