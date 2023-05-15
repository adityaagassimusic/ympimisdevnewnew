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
			<span style="font-weight: bold; color: purple; font-size: 24px;">QA KENSA CERTIFICATE EXPIRED REMINDER<br>品質保証検査認証リマインダー</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px">Certificate No.<br><small>認定番号</small></th>
							<th style="font-size: 15px">Certificate Name<br><small>認定名</small></th>
							<th style="font-size: 15px">Employee<br><small>従業員</small></th>
							<th style="font-size: 15px">Periode From<br><small>有効日付</small></th>
							<th style="font-size: 15px">Periode To<br><small>無効日付</small></th>
						</tr>
					</thead>
					<tbody align="center">
						<?php for ($i=0; $i < count($data); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">{{$data[$i]->certificate_code}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">
							{{$data[$i]->certificate_name}}
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: left;">{{$data[$i]->employee_id}} - {{$data[$i]->name}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: right;">
							{{$data[$i]->periode_from}}
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 20%; height: 20;text-align: right;">
							{{$data[$i]->periode_to}}
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:20px;" href="{{ url('index/qa/certificate/code') }}">&nbsp;&nbsp;&nbsp; Monitoring <small>監視</small> &nbsp;&nbsp;&nbsp;</a>
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
			</div>
		</center>
	</div>
</body>
</html>