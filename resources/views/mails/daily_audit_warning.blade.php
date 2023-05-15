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
		.button_reject {
		  background-color: #fa3939; /* Green */
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
			<span style="font-weight: bold; color: purple; font-size: 18px;">DAILY AUDIT WARNING - {{$data['category']}}</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;">Point Check</th>
							<th style="font-size: 15px;border: 1px solid black;">Lokasi</th>
							<th style="font-size: 15px;border: 1px solid black;">Kondisi</th>
							<th style="font-size: 15px;border: 1px solid black;">Note</th>
							<!-- <th style="font-size: 15px;border: 1px solid black;">Evidence</th> -->
							<th style="font-size: 15px;border: 1px solid black;">Leader</th>
							<th style="font-size: 15px;border: 1px solid black;">Date</th>
							<th style="font-size: 15px;border: 1px solid black;">Action</th>
						</tr>
					</thead>
					<tbody align="center">
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['data'][0]['point_check']}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['data'][0]['location']}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: center;">@if($data['data'][0]['condition'] == 'OK')
								&#9711;
							@else
								&#9747;
							@endif</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;">{{$data['data'][0]['note']}}</td>
							<!-- <td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;"><img style="width: 150px" src="{{url('http://10.109.52.1:887/miraidev/public/data_file/daily_audit/'.$data['category'].'/'.$data['data'][0]['evidence'])}}" alt=""> -->
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;">{{$data['data'][0]['auditor_id']}} - {{$data['data'][0]['auditor_name']}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;">{{$data['data'][0]['date']}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;"><a href="{{url('index/daily/audit/'.$data['data'][0]['activity_list_id'].'/'.strtolower($data['category']))}}">Cek Data</a></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
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
			</div>
		</center>
	</div>
</body>
</html>