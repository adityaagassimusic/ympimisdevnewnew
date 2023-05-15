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
			<p style="margin: 0px;">This is an automatic email from YMPI’s MIRAI system.<br>Please do not reply to this address.</p>
		</center>
		<br>
		
		<p>
			Dear {{ $data['attention'] }},<br>
			The MIRAI system has been released new PO for vendor : {{ $data['vendor_name'] }}
		</p>
		<div style="width: 80%; margin: auto;">
			<table style="border:1px solid black; border-collapse: collapse; width: 100%;">
				<thead>
					<tr>
						<th style="border:1px solid black; background-color: #aee571;">GMC</th>
						<th style="border:1px solid black; background-color: #aee571;">Material</th>
						<th style="border:1px solid black; background-color: #aee571;">Purch.doc.</th>
						<th style="border:1px solid black; background-color: #aee571;">Item Line</th>
						<th style="border:1px solid black; background-color: #aee571;">Order Date</th>
						<th style="border:1px solid black; background-color: #aee571;">ETA YMPI</th>
						<th style="border:1px solid black; background-color: #aee571;">Quantity</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['plan_delivery']); $i++) { 
						print_r('<tr>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: center;">'.$data['plan_delivery'][$i]['material_number'].'</td>');
						print_r('<td style="border: 1px solid black; width: 40%;">'.$data['plan_delivery'][$i]['material_description'].'</td>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: right;">'.$data['plan_delivery'][$i]['po_number'].'</td>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: right;">'.$data['plan_delivery'][$i]['item_line'].'</td>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: center;">'.$data['plan_delivery'][$i]['issue_date'].'</td>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: center;">'.$data['plan_delivery'][$i]['eta_date'].'</td>');
						print_r('<td style="border: 1px solid black; width: 10%; text-align: right;">'.$data['plan_delivery'][$i]['quantity'].'</td>');
						print_r('</tr>');
					}?>
				</tbody>
			</table>
		</div>
		<br>
		<p style="margin: 0px;">
			Please confirm by clicking the following link :
			<a href="https://ympi.co.id/ympicoid/public/po_confirmation?po={{ $data['po_number'] }}">po.ympi.co.id</a><br>
			For further confirmation please directly contact to our Buyer (PIC) :<br>
		</p>
		<div style="width: 50%;" style="margin-top: 0px;">
			<table style="margin-top: 0px;">
				<tr>
					<th style="padding: 0px; border: none; width: 20%; text-align: left; font-weight: normal;">Buyer</th>
					<th style="padding: 0px; border: none; width: 80%; text-align: left; font-weight: normal;">:
						<a href="mailto:{{ $data['buyer'] }}">{{ $data['buyer'] }}</a>
					</th>					
				</tr>
				<tr>
					<th style="padding: 0px; border: none; width: 20%; text-align: left; font-weight: normal;">Control</th>
					<th style="padding: 0px; border: none; width: 80%; text-align: left; font-weight: normal;">:
						<a href="mailto:{{ $data['control'] }}">{{ $data['control'] }}</a>
					</th>			
				</tr>
			</table>
		</div>
		<br>
		<br>
		<p style="font-weight: bold; margin: 0px;">
			Procurement Dept.<br>
			PT. Yamaha Musical Products Indonesia<br>
		</p>
		<p style="font-size: 14px; margin: 0px;">
			Jl. Rembang Industri I/36 Kawasan industri PIER Pasuruan<br>
		</p>

		<div style="width: 40%;" style="margin: 0px;">
			<table style="margin-top: 0px;">
				<tr>
					<th style="padding: 0px; border: none; width: 30%; text-align: left; font-weight: normal; font-size: 14px;">Phone</th>
					<th style="padding: 0px; border: none; width: 70%; text-align: left; font-weight: normal; font-size: 14px;">: 0343-740290</th>					
				</tr>
				<tr>
					<th style="padding: 0px; border: none; width: 30%; text-align: left; font-weight: normal; font-size: 14px;">Fax</th>
					<th style="padding: 0px; border: none; width: 70%; text-align: left; font-weight: normal; font-size: 14px;">: 0343-740291</th>					
				</tr>
			</table>
		</div>
		<br>
		<br>
		<br>
		<center>
			<p style="font-weight: bold; margin: 0px;">
				How to confirm the PO :<br>
			</p>
			<img style="width: 70%;" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('images\po_manual.jpg')))}}" alt=""><br>
		</center>
	</div>
</body>
</html>