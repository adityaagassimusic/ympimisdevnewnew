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
			This is an automatic notification. Please do not reply to this address.<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			<br>

			<table style="width: 80%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left">
				<thead>
					<tr>
						<td colspan="9" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
						<td style="font-weight: bold;font-size: 13px; text-align: right"><u>Perihal : Notifikasi Karyawan Salah Shift</u></td>
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
			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="text-align: left">Dear : Ummi Ernawati, Mahendra Putra<br><br>Lakukan pengecekan ulang shift karyawan, karena ada beberapa karyawan yang tidak sesuai dengan <b>Shift Schedule</b> yang ada di <b>Sunfish.</b>
						</td>
					</tr>
				</tbody>
			</table><br>
			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="background-color: #F49CBB; text-align: center"><a href="{{ url('index/shift_schedule/karyawan') }}">LAKUKAN PENGECEKAN DISINI</a></td>
					</tr>
				</tbody>
			</table><br>
			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="text-align: left">Thanks & Regards,<br><br><b><u>Management Information System Department</u></b>
						</td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>