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
			<span style="font-weight: bold; color: purple; font-size: 17px;">TBM REMINDER</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;" rowspan="2">Cat</th>
							<th style="font-size: 15px;border: 1px solid black;" rowspan="2">Item</th>
							<th style="font-size: 15px;border: 1px solid black;" colspan="{{count($data['schedule_date'])}}">Schedule</th>
						</tr>
						<tr>
							<?php for ($i=0; $i < count($data['schedule_date']); $i++) { ?>
								<th style="font-size: 15px;border: 1px solid black;background-color: rgb(126,86,134);color: white;">{{date('M-Y',strtotime($data['schedule_date'][$i]->schedule_date))}}</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody align="center">
						<?php for ($i=0; $i < count($data['location']); $i++) { ?>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 20;text-align: left;">{{strtoupper($data['location'][$i]->category)}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 15%; height: 20;text-align: left;">{{'' ?: strtoupper($data['location'][$i]->location)}} - {{strtoupper($data['location'][$i]->point_check)}} - {{strtoupper($data['location'][$i]->scan_index)}} - {{strtoupper($data['location'][$i]->specification)}}</td>
							<?php for ($j=0; $j < count($data['schedule_date']); $j++) { 
								$background = 'none';
								for ($k=0; $k < count($data['tbm']); $k++) { 
									if ($data['tbm'][$k]->concat == $data['location'][$i]->concat && $data['tbm'][$k]->schedule_date == $data['schedule_date'][$j]->schedule_date) {
										$background = '#c4c4c4';
									}
								} ?>
								<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;background-color: {{$background}}"></td>
							<?php } ?>
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
					<a style="width: 50px;text-decoration: none;font-size:16px;" href="{{url('index/maintenance/tbm')}}">&nbsp;&nbsp;&nbsp; Monitoring <small>監視</small> &nbsp;&nbsp;&nbsp;</a>
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