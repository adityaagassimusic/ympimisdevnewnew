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
					@foreach($data as $col2)
						<?php $activity_name = $col2->activity_name ?>
						<?php $department_name = $col2->department_name ?>
						<?php $date_jishu_hozen = $col2->date ?>
						<?php $jishu_hozen_id = $col2->jishu_hozen_id ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $month = $col2->month ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit Jishu Hozen<br>{{ $leader }} ({{ strtoupper($department_name) }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;color: white;">#</th>
						<th style="width: 2%; border:1px solid black;color: white;">Tanggal</th>
						<th style="width: 2%; border:1px solid black;color: white;">Bulan</th>
						<th style="width: 2%; border:1px solid black;color: white;">Mesin</th>
						<th style="width: 2%; border:1px solid black;color: white;">PIC</th>
						<th style="width: 2%; border:1px solid black;color: white;">Leader</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->month}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->nama_pengecekan}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->pic}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->leader}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/jishu_hozen/print_jishu_hozen_email/'.$activity_list_id.'/'.$jishu_hozen_id.'/'.$month) }}">Approval</a>
		</center>
	</div>
</body>
</html>