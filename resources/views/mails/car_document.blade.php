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
		.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">REMINDER PENGISIAN PERUBAHAN DOKUMEN CAR</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<br>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Nomor CPAR</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Detail</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Nomor Dokumen</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Dokumen</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Due Date</th>
						</tr>
					</thead>
					<tbody style="text-align: center;">
						@foreach($data['reminder'] as $datas)
						<tr>
							<td style="border:1px solid black;">
								{{$datas->cpar_no}}
							</td>
							<td style="border:1px solid black;">
								{{$datas->detail}}
							</td>
							<td style="border:1px solid black;">
								{{$datas->nomor_dokumen}}
							</td>
							<td style="border:1px solid black;">
								{{$datas->dokumen}}
							</td>
							<td style="border:1px solid black;">
								{{$datas->due_date}}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
					<a class="button" href="http://10.109.52.4/mirai/public/index/qc_report/grafik_cpar">Cek Monitoring CPAR</a>
				<br>
				<br>
				<p>
					<b>Thanks & Regards,</b>
				</p>
				<p>PT. Yamaha Musical Products Indonesia<br>
					Jl. Rembang Industri I / 36<br>
					Kawasan Industri PIER - Pasuruan<br>
					Phone   : 0343 – 740290<br>
					Fax.    : 0343 - 740291
				</p>
		</center>
	</div>
</body>
</html>