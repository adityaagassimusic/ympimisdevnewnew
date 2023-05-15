<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.tableBorder > tbody > tr > td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
			border: 1px solid black;
		}
		.tableBorder > thead > tr > th{
			padding-right: 5px;
			padding-left: 5px;
			border: 1px solid black;
		}
		.tableBorder {
			border-collapse: collapse;
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<span style="font-weight: bold; font-size: 24px;">Informasi Ketidaksesuaian Spesifikasi Indirect Material</span><br>
			<span style="color: red; font-weight: bold;">({{ $data['title'] }})</span>
		</center>
		<div style="width: 90%; margin: auto;">
			<table style="width: 60%;">
				<tbody>
					<tr>
						<td style="font-weight: bold;">Material</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->material_number }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Deskripsi</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->material_description }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Tanggal Kedatangan</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('l, d F Y', strtotime($data['material_check']->posting_date)) }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Vendor</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->vendor_code }} - {{ $data['material_check']->vendor_name }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Diterima Oleh</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->received_by }} - {{ $data['material_check']->received_by_name }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Dicek Oleh</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->checked_by }} - {{ $data['material_check']->checked_by_name }} ({{ $data['material_check']->location }})</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Total Sample</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->sample_qty }} {{ $data['material_check']->uom }}(s)</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Total NG</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['material_check']->ng_count }} {{ $data['material_check']->uom }}(s)</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Persentase NG</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ round(($data['material_check']->ng_count/$data['material_check']->sample_qty)*100, 1) }}%</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Persentase NG</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ round(($data['material_check']->ng_count/$data['material_check']->sample_qty)*100, 1) }}%</td>
					</tr>
					@if($data['position'] == 'Vendor')
					<tr>
						<td style="font-weight: bold;">Estimasi Kedatangan</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('l, d F Y', strtotime($data['eta_date'])) }}</td>
					</tr>
					@endif
					@if($data['position'] == 'Arrived')
					<tr>
						<td style="font-weight: bold; background-color: #00a65a;">Tanggal Datang</td>
						<td style="font-weight: bold; background-color: #00a65a;">:</td>
						<td style="background-color: #00a65a;">{{ date('l, d F Y', strtotime($data['arrived_date'])) }}</td>
					</tr>
					@endif
				</tbody>
			</table>
			<br>
			<table class="tableBorder" style="border-color: black; width: 90%;" >
				<thead style="background-color: #dd4b39; color: white;">
					<tr>
						<th style="width: 0.1%; text-align: center;">#</th>
						<th style="width: 2.5%; text-align: left;">Detail NG</th>
						<th style="width: 0.1%; text-align: center;">Jumlah NG</th>
						<th style="width: 1%; text-align: center;">Bukti Foto</th>
					</tr>
				</thead>
				<tbody>
					<?php $count = 0;?>
					@foreach ($data['material_check_findings'] as $row)
					<tr>
						<?php $count += 1; ?>
						<td style="width: 0.1%; text-align: center;">{{ $count }}</td>
						<td style="width: 2.5%; text-align: left;">{{ $row->remark }}</td>
						<td style="width: 0.1%; text-align: center;">{{ $row->quantity }}</td>
						<td style="width: 1%; text-align: center;">
							<img src="data:image/jpg;base64,{{ base64_encode(file_get_contents(public_path('files/material_check/'.$row->evidence_file))) }}" alt="" style="width: 180px; padding-top: 5px; padding-bottom: 5px;">
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<table class="tableBorder" style="border-color: black; width: 90%;" >
				<thead style="background-color: #dd4b39; color: white;">
					<tr>
						<th style="width: 0.1%; text-align: center;">Catatan Foreman</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 0.1%;"><?= $data['foreman']->report ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			@if($data['position'] == 'Buyer' || $data['position'] == 'Vendor')
			<table class="tableBorder" style="border-color: black; width: 90%;" >
				<thead style="background-color: #dd4b39; color: white;">
					<tr>
						<th style="width: 0.1%; text-align: center;">Catatan Buyer</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 0.1%;"><?= $data['buyer']->report ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			@endif

			<center>
				<a href="{{ url('/index/material/check_monitoring') }}">Menuju Monitoring Pengecekan Kedatangan Material Indirect.</a>
			</center>
		</div>
	</div>
</body>
</html>