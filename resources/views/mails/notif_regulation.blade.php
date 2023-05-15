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
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<h2>
				Regulasi Belum Dilakukan Implementasi
			</h2>
			Ini adalah pesan otomatis. Jangan membalas email ini.
			<br>
			<h2>{{$data->vendor}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Poin</th>
						<th style="width: 4%; border:1px solid black;">Isi</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Deskripsi</td>
						<td style="border:1px solid black; text-align: left !important;">{{$data->description}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Departemen</td>
						<td style="border:1px solid black; text-align: left !important;">{{$data->department_shortname}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Tanggal Penyelesaian</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($data->status_due_date)) ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Remark</td>
						<td style="border:1px solid black; text-align: left !important;">{{$data->remark}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Analisis</td>
						<td style="border:1px solid black; text-align: left !important;">{{$data->analisis}}</td>
					</tr>
				</tbody>
			</table>
			<br><br>
			<a href="http://10.109.52.4/mirai/public/index/general/agreement">Klik Disini Untuk Update Regulasi</a>
		</center>
	</div>
</body>
</html>