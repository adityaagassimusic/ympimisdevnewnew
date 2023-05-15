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
			<p style="font-size: 18px;">Audit NG Jelas<br>{{ $data['datas'][0]->leader_dept }} ({{ strtoupper($data['datas'][0]->department_shortname) }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);color: white;">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">Dept</th>
						<th style="width: 2%; border:1px solid black;">Product</th>
						<th style="width: 2%; border:1px solid black;">Process</th>
						<th style="width: 2%; border:1px solid black;">Result</th>
						<th style="width: 2%; border:1px solid black;">PIC</th>
						<th style="width: 2%; border:1px solid black;">Auditor</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data['datas'] as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->department_shortname}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->product}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->proses}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->kondisi}}</td>
						<?php $pic = ''; ?>
						<?php $auditor = ''; ?>
						<?php for ($j=0; $j < count($data['emp']); $j++) { 
							if ($data['emp'][$j]->employee_id == $col->pic) {
								$pic = $data['emp'][$j]->name;
							}
							if ($data['emp'][$j]->employee_id == $col->auditor) {
								$auditor = $data['emp'][$j]->name;
							}
						} ?>
						<td style="border:1px solid black; text-align: center;">{{$col->pic}} - {{$pic}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->auditor}} - {{$auditor}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/production_audit/print_audit_email/{{$data['datas'][0]->activity_list_id}}/{{$data['datas'][0]->month}}/{{$data['datas'][0]->product}}/{{$data['datas'][0]->proses}}">Approval</a><br>
		</center>
	</div>
</body>
</html>