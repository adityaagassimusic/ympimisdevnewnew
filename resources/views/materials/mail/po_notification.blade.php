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
		

		<center>
			<p style="font-size: 24px; background-color: #b762c1; width: 50%; margin: 0px;">
				<b>Purchase Order Confirmation Report</b><br>
				<b>PT Yamaha Musical Products Indonesia</b>
			</p>

			<p style="font-size: 22px; background-color: #dfa2e7; width: 50%; margin: 0px;">
				Vendor Code : {{ $data['vendor_code'] }}<br>
				Vendor Name : {{ $data['vendor_name'] }}<br>
				PO Number : {{ $data['po_number'] }}<br>
				Confirmation At : {{ $data['delivery']['po_confirm_at'] }}<br>
				@if(count($data['notes']) > 0 )
				Note :
				@else
				Note : -<br>
				@endif
			</p>


			@php
			if(count($data['notes']) > 0){
				print_r('<table>');
				for ($i=0; $i < count($data['notes']); $i++) { 
					print_r('<tr>');
					print_r('<td style="background-color: #dfa2e7; padding: 5px; border: none; width: 20%; text-align: center;">'.$data['notes'][$i]['item_line'].'</td>');
					print_r('<td style="background-color: #dfa2e7; padding: 5px; border: none; width: 20%; text-align: center;">'.$data['notes'][$i]['material_number'].'</td>');
					print_r('<td style="background-color: #dfa2e7; padding: 5px; border: none; width: 30%;">'.$data['notes'][$i]['material_description'].'</td>');
					print_r('<td style="background-color: #dfa2e7; padding: 5px; border: none; width: 30%;">'.$data['notes'][$i]['note'].'</td>');
					print_r('</tr>');
				}
				print_r('</table>');
			}
			@endphp

			<p style="font-size: 24px; background-color: #dfa2e7; width: 50%; margin: 0px;">
				<b>Thank you for confirming the PO via website YMPI</b>
			</p>

		</center>


	</div>
</body>
</html>