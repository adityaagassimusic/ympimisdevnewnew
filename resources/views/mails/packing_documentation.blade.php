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
			<span style="font-weight: bold; color: purple; font-size: 17px;">{{$data['title']}} ON {{strtoupper(date("d-M-Y"))}}</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;width: 1%">#</th>
							<th style="font-size: 15px;border: 1px solid black;width: 10%;">Material</th>
							<th style="font-size: 15px;border: 1px solid black;width: 5%;">Serial Number</th>
						</tr>
					</thead>
					<tbody align="center">
						<?php $index = 1; ?>
						<?php for ($i=0; $i < count($data['latch']); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 20;text-align: right;">{{$index}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 10%; height: 20;text-align: left;">{{$data['latch'][$i]->material_number}} - {{$data['latch'][$i]->material_description}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['latch'][$i]->serial_number}}</td>
						</tr>
						<?php
							$index++;
						 } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br>
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="http://10.109.52.4/mirai/public/report/latch/clarinet">&nbsp;&nbsp;&nbsp; Report &nbsp;&nbsp;&nbsp;</a>
				<br>
				<br>
			</div>
		</center>
	</div>
</body>
</html>