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
			<span style="font-weight: bold; color: purple; font-size: 18px;">LIFETIME {{strtoupper($data['category'])}} {{strtoupper($data['location'])}}</span><br>
			<!-- <span style="color: red">Limit {{ucwords($data['category'])}} {{ucwords($data['location'])}} has been reached. {{ucwords($data['category'])}} must be repaired.</span> -->
			<span style="color: red">{{ucwords($data['category'])}} {{ucwords($data['location'])}} akan dilakukan Repair ke Workshop.</span>
			<br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 70%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;border: 1px solid black;">Product</th>
							<th style="font-size: 15px;border: 1px solid black;">Item</th>
							<th style="font-size: 15px;border: 1px solid black;">Made In</th>
							<!-- <th style="font-size: 15px;border: 1px solid black;">Limit</th> -->
							<th style="font-size: 15px;border: 1px solid black;">Lifetime</th>
							<th style="font-size: 15px;border: 1px solid black;">Repair</th>
							<th style="font-size: 15px;border: 1px solid black;">Reason</th>
						</tr>
					</thead>
					<tbody align="center">
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['lifetime']->product}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['lifetime']->item_name}} {{$data['lifetime']->item_type}} ({{$data['lifetime']->item_index}})</td>
							<td style="border:1px solid black; font-size: 15px; width: 5%; height: 20;text-align: left;">{{$data['lifetime']->item_made_in}}</td>
							@if(strtolower($data['category']) == 'screwdriver')
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: right;">{{$data['days']}}</td>
							@else
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: right;">{{$data['lifetime']->lifetime}}</td>
							@endif
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: right;">{{$data['lifetime']->repair}}</td>
							<td style="border:1px solid black; font-size: 15px; width: 2%; height: 20;text-align: left;">{{$data['reason']}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
				<!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br> -->
					<!-- <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/repair/lifetime/'.$data['category'].'/'.$data['location'].'/'.$data['lifetime']->id) }}">&nbsp;&nbsp;&nbsp; Repair & Buat WJO &nbsp;&nbsp;&nbsp;</a> -->
				<!-- <br> -->
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