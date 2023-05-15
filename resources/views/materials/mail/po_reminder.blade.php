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
			We have released new PO  {{ $data['po_number'] }} on {{ $data['po_send_at'] }}.<br>
			But we didn’t get any confirmation from you.<br>

			We gentle remind you to immediately confirm the PO {{ $data['po_number'] }} via the following link : 
			<a href="https://ympi.co.id/ympicoid/public/po_confirmation?po={{ $data['po_number'] }}">po.ympi.co.id</a><br>
		</p>
		<p style="margin: 0px;">
			For further confirmation please directly contact to our Buyer (PIC) :
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