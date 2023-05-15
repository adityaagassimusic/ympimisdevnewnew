<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		td {
			padding: 3px;
		}
		.border1 {
			border: 1px solid black;
		}

		body {
			font-family: sans-serif;
		}
		thead {
			font-size: 9px;
			font-weight: bold;
			text-align: center;
		}
		thead>tr>td {
			background-color: #a7f2b0
		}

		tbody {
			font-size: 8px;
		}
	</style>
</head>
<body>
	<div>
		<center><h4>Fix Assets Audit</h4></center>
		<table width="100%" style="border: 1px solid black">
			<tr>
				<td>
					<h4>PT Yamaha Musical Products Indonesia</h4> 
				</td>
				<td rowspan="2" style="font-size: 12px">
					<center>
						Tanggal : 
						<?php 
						if ($audit_data[0]->appr_manager_at) {
							echo $audit_data[0]->appr_manager_at;
						} else {
							echo "___________ ";
						}
						?>
						<br>
						Diketahui oleh 
						<br>
						<?php 
						if ($audit_data[0]->appr_manager_by) {
							echo $audit_data[0]->appr_manager_by;
						} else {
							echo "<br><br>";
						}
						?>
						<br>
						
						Manager
					</center>
				</td>
			</tr>
			<tr>
				<td style="font-size: 12px">Control Section : {{ $audit_data[0]->asset_section }}</td>
			</tr>
		</table>
		<table width="100%" style="border: 1px solid black">
			<thead>
				<tr>
					<td>No</td>
					<td style="border-bottom: 1px solid black" rowspan="2">SAP ID</td>
					<td style="border-bottom: 1px solid black" rowspan="2">Gambar</td>
					<td colspan="2" class="border1">Keberadaan</td>
					<td colspan="5" class="border1">Kondisi Pengecualian</td>
					<td colspan="3" class="border1">TTD</td>
				</tr>
				<tr>
					<td style="border-bottom: 1px solid black">Deskripsi</td>
					<td class="border1" style="font-size: 8px">Ada</td>
					<td class="border1" style="font-size: 8px">Tidak Ada</td>
					<td class="border1" style="font-size: 8px">Tidak Digunakan</td>
					<td class="border1" style="font-size: 8px">Asset Rusak</td>
					<td class="border1" style="font-size: 8px">Label Tidak Ada/Rusak</td>
					<td class="border1" style="font-size: 8px">Map Tidak Sesuai</td>
					<td class="border1" style="font-size: 8px">Lain - lain</td>
					<td class="border1" style="font-size: 8px">Cek I</td>
					<td class="border1" style="font-size: 8px">Cek II</td>
					<td class="border1" style="font-size: 8px">Audit</td>
				</tr>
			</thead>
			<tbody>
				@foreach($audit_data as $audit)
				<tr>
					<td>{{ $audit->id }}</td>
					<td>{{ $audit->sap_number }}</td>
					<td rowspan="3" style="border-bottom: 1px solid black">
						<img src="{{ url('files/fixed_asset/asset_picture/'.$audit->asset_images)}}" style="max-width: 60px" >
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->availability == 'Ada') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->availability == 'Tidak Ada') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->usable_condition == 'Tidak Digunakan') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->asset_condition == 'Rusak') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->label_condition == 'Rusak') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">
						<center style="font-weight : bold">
							<?php 
							if ($audit->map_condition == 'Rusak') {
								echo 'V';
							}
							?>
						</center>
					</td>
					<td rowspan="3" class="border1">{{ $audit->note }}</td>
					<td rowspan="3" class="border1">
						<?php 
						if ($audit->category == 'MPI') {
							if ($audit->check_one_by) {
								echo explode('/', $audit->check_one_by)[1];
							}
						} else {
							if ($audit->check_one_by) {
								echo $audit->check_one_by;
							}
						}
						?>
					</td>
					<td rowspan="3" class="border1">
						<?php 
						if ($audit->category == 'MPI') {
							if ($audit->check_two_by) {
								echo explode('/', $audit->check_two_by)[1];
							}
						} else {
							if ($audit->check_two_by) {
								echo $audit->check_two_by;
							}
						}?>
					</td>
					<td rowspan="3" class="border1">
						<?php if ($audit->checked_date && $audit->remark) {
							echo $audit->checked_by;
						} ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">{{ $audit->asset_name }} 
						<?php if ($audit->audit_type == 'Remote'): ?>
							<br> : <b>Remote Audit</b>
						<?php endif ?>
					</td>
				</tr>
				<tr>
					<td style="border-bottom: 1px solid black">{{ $audit->reg_date }}</td>
					<td style="border-bottom: 1px solid black">{{ $audit->location }}</td>
				</tr>
				@endforeach
			</tbody>

		</table>

	</div>
</body>
</html>