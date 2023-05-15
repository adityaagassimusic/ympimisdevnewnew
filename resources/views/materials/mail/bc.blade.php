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

		<div style="width: 100%; margin: auto;">
			<center>
				<table style="border: none; width: 60%;">
					<tr>
						<th style="font-size: 18px; background-color: #b762c1; padding-bottom: 15px; padding-top: 15px; font-weight: bold;">
							<p>
								Document SPPB & BC Report<br>
								PT Yamaha Musical Products Indonesia
							</p>
						</th>
					</tr>
					<tr>
						<td style="font-size: 14px; background-color: #dfa2e7; padding-bottom: 10px; padding-top: 10px; text-align: center;">
							<p>
								Vendor Code : {{ $data['vendor_code'] }}<br>
								Vendor Name : {{ $data['vendor_name'] }}<br>
								No Surat Jalan : {{ str_replace('.pdf', '', implode(',', json_decode($data['do_number']))) }}<br>
								No. SPPB & BC : {{ explode("_", $data['bc_document'])[0] }} & {{ explode("_", $data['sppb'])[0] }}<br>
								Date Release : {{ date("d M Y") }}<br>
							</p>
						</td>
					</tr>				
					<tr>
						<th style="font-size: 16px; background-color: #dfa2e7; padding-bottom: 10px; padding-top: 10px; font-weight: bold; vertical-align: middle;">
							Thank you for your feedback on YMPI's website
						</th>
					</tr>
				</table>
			</center>
		</div>
	</div>
</body>
</html>