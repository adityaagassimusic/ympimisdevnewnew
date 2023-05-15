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
			<p style="font-size: 20px; font-weight: bold;">New Invoice Asset Has Been Uploaded <br> Please Register Fixed Asset</p>
			This is an automatic notification. Please do not reply to this address.
			<p style="padding: 0px !important; font-weight: bold; font-size: 30px">Form Id : {{ $data->form_id }}</p>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Investment Number</th>
					<td style="width: 50%">{{ $data->investment_number }}</td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Categories</th>
					<td style="width: 50%">{{ $data->type }}</td>
				</tr>
			</table>
			<br>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/registration_asset_form") }}">&nbsp;&nbsp;&nbsp; Fixed Asset List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			
		</center>
	</div>
</body>
</html>