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
			<p style="font-size: 22px; font-weight: bold;">3M Document(s) Requirement </p>
			<p>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br><br>
			<p style="font-size: 18px; font-weight: bold">
				This 3M Application has been meeting <br>
				And Have unmet Document Requirement <br>
				Please Upload The Document
			</p>
			<table style="border-color: black; width: 80%">
				<tr>
					<th style="text-align: left; background-color: #c79cf7; width: 20%">Reference Number : </th>
					<td>{{ $data['tiga_m']->sakurentsu_number }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M Title : </th>
					<td>{{ $data['tiga_m']->title }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Product Name : </th>
					<td>{{ $data['tiga_m']->product_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Proccess Name : </th>
					<td>{{ $data['tiga_m']->proccess_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Unit Name : </th>
					<td>{{ $data['tiga_m']->unit }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">3M Category : </th>
					<td>{{ $data['tiga_m']->category }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7; ">Related Department : </th>
					<td>{{ $data['tiga_m']->related_department }}</td>
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
						<th style="width: 15%; border:1px solid black;">PIC</th>
						<th style="width: 20%; border:1px solid black;">Document Name</th>
						<th style="border:1px solid black;">Note</th>
						<th style="width: 10%; border:1px solid black;">Target Date</th>
						<th style="width: 10%; border:1px solid black;">Finish Date</th>
					</tr>
				</thead>
				<tbody>
					<?php $num = 1; ?>
					@foreach($data['documents'] as $doc)
					<tr>
						<td>{{ $num }}</td>
						<td>{{ $doc->name }}</td>
						<td>{{ $doc->document_name }}</td>
						<td>{{ $doc->document_description }}</td>
						<td>{{ $doc->target }}</td>
						<td>{{ $doc->finish }}</td>
					</tr>
					<?php $num++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
			<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/sakurentsu/3m/document/upload/'.$data['form_id']) }}">&nbsp;&nbsp;&nbsp; Upload Document &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: #4ecc4b;color: white;font-size:20px; text-decoration: none" href="{{ url('detail/sakurentsu/3m/'.$data['form_id'].'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp;</a>
		</center>
	</div>
</body>
</html>