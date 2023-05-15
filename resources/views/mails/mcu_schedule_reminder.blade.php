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
			<span style="font-weight: bold; color: purple; font-size: 24px;">Reminder Medical Check Up</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: left;">
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 1%">#</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 1%">ID</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 3%">Name</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 1%">Dept</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 2%">Sect</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 2%">Group</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 2%">Sub Group</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;width: 2%">Schedule Date</th>
						</tr>
					</thead>
					<tbody style="text-align: left;">
						<?php $index  =1; ?>
						@foreach($data as $data)
						<tr>
							<td style="border:1px solid black;">
								{{$index}}
							</td>
							<td style="border:1px solid black;">
								{{$data['employee_id']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['name']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['department']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['section']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['group']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['sub_group']}}
							</td>
							<td style="border:1px solid black;text-align: right;">
								{{$data['schedule_date']}}
							</td>
						</tr>
						<?php $index++; ?>
						@endforeach
					</tbody>
				</table>
				<!-- <br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a class="button" href="http://10.109.52.4/mirai/public/index/ga_control/mcu/monitoring/physical">Monitoring</a>
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
				</p> -->
		</center>
	</div>
</body>
</html>