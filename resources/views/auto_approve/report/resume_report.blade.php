<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		td {
			padding: 3px;
		}
		.border1 {
			border: 1px solid black;
		}

		@page { margin: 5px; }
		body { margin: 5px; }
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%" border="1">
				<tr>
					<td width="20%">
						<center>
							<img src="{{ url("waves2.png")}}" style="width: 120px"> <br>

							<b style="font-size: 8px;">PT.Yamaha Musical Products Indonesia</b>
						</center>
					</td>
					<td style="font-size: 18px">
						<center>
							<b>MIRAI APPROVAL</b>
						</center>
					</td>
					<td style="font-size: 10px;" width="23%">
						Dokumen No : YMPI/STD/FK3/029 <br>
						Revisi No : 06 <br>	
						Tanggal : 09 Agustus 2019 <br>
					</td>
				</tr>
			</table>
			<table width="100%" border="1" style="margin-top: 5px">
				<tr>
					<td colspan="2">
						<b style="font-size: 13px">I. PENGAJUAN TRIAL</b>
						<sup style="font-size: 10px">*di isi oleh bagian yang mengajukan trial</sup>
					</td>
				</tr>
				<tr>
					<td style="font-size: 12px" width="100%" colspan="2">
						<b>Kepada Yth.</b> <br>
						<table width="100%">
							<tr>
								<td>Nama</td>
								<td>..</td>
								<td>Tanggal Pengajuan</td>
								<td>{{$detail->no_transaction}}</td>
								<td>No. Referensi</td>
								<td>{{$detail->no_transaction}}</td>
							</tr>
							<tr>
								<td>Departemen</td>
								<td style="width: 25%">{{$detail->no_transaction}}</td>
								<td>Tanggal Trial</td>
								<td>{{$detail->no_transaction}}</td>
								<td>Total APD/Material</td>
								<td>..</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>
	</div>
</body>
</html>