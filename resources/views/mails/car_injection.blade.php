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
			<span style="font-weight: bold; color: purple; font-size: 24px;">INFORMATION INJECTION VISUAL CHECK</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>			
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="60%">
					<tbody>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Item</td>
							<td style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Detail</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">Machine</td>
							<td style="border:1px solid black;font-weight: bold;">{{$data[0]['machine']}}</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Timing
							</td>
							<td style="border:1px solid black;font-weight: bold;">
								{{$data[0]['hour_check']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Material
							</td>
							<td style="border:1px solid black;font-weight: bold;">
								{{$data[0]['material_number']}} - {{$data[0]['part_name']}} {{$data[0]['part_type']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Cavity
							</td>
							<td style="border:1px solid black;font-weight: bold;">
								{{$data[0]['cavity']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								Molding
							</td>
							<td style="border:1px solid black;font-weight: bold;">
								{{$data[0]['molding']}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black;font-weight: bold;background-color: #d4e157;">
								PIC Cek
							</td>
							<td style="border:1px solid black;font-weight: bold;">
								{{$data[0]['pic_name']}}
							</td>
						</tr>
					</tbody>
				</table>
		</center>
	</div>
	<br>
	<br>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Cav Detail</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Point</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Result
							</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">CAR Description
							</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">CAR Immediately Action
							</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">CAR Possibility Cause
							</th>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white">Corrective Action
							</th>
						</tr>
					</thead>
					<tbody style="text-align: center;">
						<?php $id_car = []; ?>
						@foreach($data as $data)
						<?php array_push($id_car,$data['id']) ?>
						<tr>
							<td style="border:1px solid black;">
								{{$data['cav_detail']}}
							</td>
							<td style="border:1px solid black;">
								{{$data['point_check']}}
							</td>
							<td style="border:1px solid black;">
								<?php if($data['result_check'] == 'NS'){
								echo 'Butuh Pengawasan';
								}else{
								echo $data['result_check'];
								} ?>
							</td>
							<td style="border:1px solid black;">
								<?php echo $data['car_description'] ?>
							</td>
							<td style="border:1px solid black;">
								<?php echo $data['car_action_now'] ?>
							</td>
							<td style="border:1px solid black;">
								<?php echo $data['car_cause'] ?>
							</td>
							<td style="border:1px solid black;">
								<?php echo $data['car_action'] ?>
							</td>
						</tr>
				@endforeach
					</tbody>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a class="button" href="{{ url('approval/injection/visual/'.join('_',$id_car)) }}">Accept</a>
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