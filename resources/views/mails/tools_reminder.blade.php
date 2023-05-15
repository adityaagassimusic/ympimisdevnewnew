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
	</style>
</head>
<body>
	<div>
		<center>


			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Reminder Order Tools <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<!-- <h2>Reminder Order Tools Equipment</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Item Code</th>
						<th style="width: 2%; border:1px solid black;">Description</th>
						<th style="width: 2%; border:1px solid black;">Rack Code</th>
						<th style="width: 2%; border:1px solid black;">Location</th>
						<th style="width: 2%; border:1px solid black;">Group</th>
						<th style="width: 2%; border:1px solid black;">Qty Order</th>
					</tr>
				</thead>
				<tbody>
				@foreach($data as $datas)
					<tr>
						<td style="width: 2%; border:1px solid black;">{{$datas->item_code}}</td>
					
						<td style="width: 2%; border:1px solid black;">{{$datas->description}}</td>
					
						<td style="width: 2%; border:1px solid black;">{{$datas->rack_code}}</td>
					
						<td style="width: 2%; border:1px solid black;">{{$datas->location}}</td>
				
						<td style="width: 2%; border:1px solid black;">{{$datas->group}}</td>
				
						<td style="width: 2%; border:1px solid black;">{{$datas->qty}}</td>
					</tr>
				@endforeach
				</tbody>
			</table> -->

			<h2>Reminder Order Tools</h2>
			<h3>Sisa Budget Bulan ini (WEL22001) : $270.19</h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Item Code</th>
						<th style="width: 2%; border:1px solid black;">Description</th>
						<!-- <th style="width: 2%; border:1px solid black;">Rack Code</th> -->
						<th style="width: 2%; border:1px solid black;">Location</th>
						<!-- <th style="width: 2%; border:1px solid black;">Group</th> -->
						<th style="width: 2%; border:1px solid black;">Nov-22</th>
						<th style="width: 2%; border:1px solid black;">Dec-22</th>
						<th style="width: 2%; border:1px solid black;">Jan-23</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">D002022</td>
					
						<td style="width: 2%; border:1px solid black;">Drill dia 1.4x300L</td>
					
						<td style="width: 2%; border:1px solid black;">Welding Process</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">2 <br> ($124)</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">1 <br> ($62)</td>

						<td style="width: 2%; border:1px solid black;text-align: center">1 <br> ($60)</td>						
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">D002131</td>
					
						<td style="width: 2%; border:1px solid black;">Drill/ mata bor dia. 2.35x40x300</td>
					
						<td style="width: 2%; border:1px solid black;">Welding Process</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">3 <br> ($183)</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">2 <br> ($122)</td>

						<td style="width: 2%; border:1px solid black;text-align: center">3 <br> ($183)</td>						
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">D002114</td>
					
						<td style="width: 2%; border:1px solid black;">Drill/mata bor d. 1.4x200L</td>
					
						<td style="width: 2%; border:1px solid black;">Welding Process</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">1 <br> ($48)</td>
				
						<td style="width: 2%; border:1px solid black;text-align: center">1 <br> ($48)</td>

						<td style="width: 2%; border:1px solid black;text-align: center">0 <br> ($0)</td>						
					</tr>
				</tbody>
			</table>

			<br><br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk Melihat Oder</i> &#8650;</span><br>
			<a href="http://10.109.52.1:887/miraidev/public/tools/need_order">Order Now</a><br>	
		</center>
	</div>
</body>
</html>