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
	<?php $nama_tim = $row->nama_tim ?>
	<?php $nama = $row->nama ?>
	<?php $department = $row->department ?>
	<?php $score = $row->score ?>
	<?php $remark = $row->remark ?>
	<?php $soal = $row->soal ?>
	@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			This is an automatic notification. Please do not reply to this address.<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			<br>

			<table style="width: 95%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
				<thead>
					<tr>
						<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
						<td style="font-weight: bold;font-size: 13px; text-align: right">Judul KYT : {{$remark}}</td>
					</tr>
					<tr>
						<td colspan="9" style="text-align: left;font-size: 13px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
					</tr>
					<tr>
						<td colspan="9" style="text-align: left;font-size: 13px">Phone : (0343) 740290 Fax : (0343) 740291</td>
					</tr>
					<tr>
						<td colspan="10" style="text-align: left;font-size: 13px">Jawa Timur Indonesia</td>
					</tr>
				</thead>
			</table><br>
			<table style="border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="background-color: #F49CBB; text-align: center">LAKUKAN SOSIALISASI ULANG</td>
					</tr>
				</tbody>
			</table><br>
			<table style="border:1px solid black; border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #D0DDD7;">Kode Soal</td>
						<td style="width: 2%; border:1px solid black;">{{ $soal }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #D0DDD7;">Nama Tim</td>
						<td style="width: 2%; border:1px solid black;">{{ $nama_tim }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #D0DDD7;">Nama Ketua Tim</td>
						<td style="width: 2%; border:1px solid black;">{{ $nama }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #D0DDD7;">Department</td>
						<td style="width: 2%; border:1px solid black;">{{ $department }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: #D0DDD7;">Nilai</td>
						<td style="width: 2%; border:1px solid black;">{{ $score }}</td>
					</tr>
				</tbody>
			</table><br>
			<table style="border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="text-align: left">Tim tersebut telah mendapatkan penilaian kurang dari ketentuan, lakukan sosialisasi ulang agar tim tersebut lebih memahami terkait <b>{{$remark}}</b></td>
					</tr>
				</tbody>
			</table><br><br><br><br>
			<table style="border-collapse: collapse;" width="95%">
				<tbody>
					<tr>
						<td style="text-align: center"><a href="{{ url('index/sosialisasi_ulang/kyt/'.$nama_tim.'/'.$nama_tim.'') }}">Klik Untuk Melakukan Sosialisasi Terkait {{ $remark }}</a></td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>