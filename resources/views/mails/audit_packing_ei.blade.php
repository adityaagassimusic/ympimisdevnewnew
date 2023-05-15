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
		/*.button {
		  background-color: #4CAF50;
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
		}*/
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Temuan Audit Packing FG / KD (梱包監査)</span><br>
			<p>This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="80%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: #c2ff7d;color: black">Detail Temuan</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Product</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->product}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Material</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->material_number}} - {{$data->material_description}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Material Audited</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->material_audited}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Urutan</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->point_check}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Point Check</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->point_check_details}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Standard</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->standard}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Defect Category</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->defect_category}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Qty</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->qty_ng}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left: 10px">Detail NG</th>
							<td style="border:1px solid black;text-align: left;">
								Nama NG : <?php echo explode('_', $data->ng_detail)[0] ?><br>
								Area : <?php echo explode('_', $data->ng_detail)[1] ?><br>
								Pallet : <?php echo explode('_', $data->ng_detail)[2] ?><br>
								Baris : <?php echo explode('_', $data->ng_detail)[3] ?><br>
								Box : <?php echo explode('_', $data->ng_detail)[4] ?><br>
								Line : <?php echo explode('_', $data->ng_detail)[5] ?><br>
								Emp : <?php echo explode('_', $data->ng_detail)[6] ?> - <?php echo explode('_', $data->ng_detail)[7] ?><br>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left: 10px">Note</th>
							<td style="border:1px solid black;text-align: left;">
								<?php echo $data->note ?>
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Due Date</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->due_date}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);color: white;text-align: left;padding-left:10px;">Lot Status</th>
							<td style="border:1px solid black;text-align: left;padding-left:10px;">
								{{$data->status_lot}}
							</td>
						</tr>
						
					</thead>
				</table>
				<br>
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a href="http://10.109.52.4/mirai/public/index/qa/packing/handling/{{$data->audit_id}}">Input Penanganan</a><br>
					<br>
					<a href="http://10.109.52.4/mirai/public/index/qa/packing">Monitoring</a>
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