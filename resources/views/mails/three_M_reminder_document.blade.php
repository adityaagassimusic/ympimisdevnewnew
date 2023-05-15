<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;			
		}
		th {
			padding: 2px;
		}
		td {
			padding: 2px;
			text-align: left !important;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 22px; font-weight: bold;">3M Document(s) Requirement Reminder</p>
			<p>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<p style="font-size: 18px; font-weight: bold">
				This 3M Application has been submited from <b id="req_date">{{ $data['datas']->create_date }}</b><br>
				And Have unmet Document Requirement <br>
				Please Upload The Document
			</p>
			<table style="border-color: black; width: 80%">
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M Number : </th>
					<td>{{ $data['datas']->form_identity_number }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Reference Number : </th>
					<td>{{ $data['datas']->sakurentsu_number }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M Title : </th>
					<td>{{ $data['datas']->title }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Product Name : </th>
					<td>{{ $data['datas']->product_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Proccess Name : </th>
					<td>{{ $data['datas']->proccess_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Unit Name : </th>
					<td>{{ $data['datas']->unit }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M Category : </th>
					<td>{{ $data['datas']->category }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Plan Change Date : </th>
					<td>{{ $data['datas']->started_date }} ( {{ $data['datas']->date_note }} ) </td>
				</tr>
			</table>
			<br>
			<table style="border-color: black; width: 80%;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="6" style="background-color: #9cb8ff">Document List</th>
					</tr>
					<tr style=" background-color: #9cb8ff">
						<th style="width: 1%; border:1px solid black;">No</th>
						<th style="border:1px solid black;">PIC</th>
						<th style="width: 20%; border:1px solid black;">Document Name</th>
						<th style="width: 15%; border:1px solid black;">Note</th>
						<th style="width: 10%; border:1px solid black;">Target Date</th>
					</tr>
				</thead>
				<tbody>
					<?php $num = 1; ?>
					<tr>
						<td>{{ $num }}</td>
						<td>{{ $data['datas']->pic }}</td>
						<td>{{ $data['datas']->document_name }}</td>
						<td>{{ $data['datas']->document_description }}</td>
						<td>{{ $data['datas']->target_date }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
			<a style="background-color: #73ff70; width: 50px;text-decoration: none;font-size:20px;" href="http://10.109.52.1:887/miraidev/public/index/sakurentsu/3m/document/upload/{{ $data['datas']->id }}">&nbsp;&nbsp;&nbsp; Upload Document &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</center>
	</div>
</body>
</html>