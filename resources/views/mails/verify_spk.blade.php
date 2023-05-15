<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	@foreach($data as $row)
	<?php $order_no = $row->order_no ?>
	<?php $section = $row->section ?>
	<?php $target_date = $row->target_date ?>
	<?php $type = $row->type ?>
	<?php $danger = $row->danger ?>
	<?php $description = $row->description ?>
	<?php $safety_note = $row->safety_note ?>
	<?php $pemohon = $row->name ?>
	@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Verify Maintenance Job Order (SPK)</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Order No.</td>
						<td style="width: 2%; border:1px solid black;">{{ $order_no }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Pemohon</td>
						<td style="width: 2%; border:1px solid black;">{{ $pemohon }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Bagian</td>
						<td style="width: 2%; border:1px solid black;">{{ $section }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Jenis Pekerjaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $type }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Potensi Bahaya</td>
						<td style="width: 2%; border:1px solid black;">{{ $danger }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Uraian Permintaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $description }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Target Selesai</td>
						<td style="width: 2%; border:1px solid black;">{{ $target_date }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;"><i>Tekan tombol dibawah ini untuk melakukan verifikasi potensi bahaya</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("verify/maintenance/spk/danger_note/".$order_no) }}">&nbsp;&nbsp;&nbsp; Verifikasi &nbsp;&nbsp;&nbsp;</a>
		</center>
	</div>
</body>
</html>