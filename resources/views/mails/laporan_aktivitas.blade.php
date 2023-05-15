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
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $section = $col2->section ?>
						<?php $subsection = $col2->subsection ?>
						<?php $date = $col2->date ?>
						<?php $month = date("F Y", strtotime($col2->date)); ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit IK<br>{{ $leader }} ({{ strtoupper($department_name) }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">Nama Dokumen</th>
						<th style="width: 2%; border:1px solid black;">No. Dokumen</th>
						<th style="width: 2%; border:1px solid black;">Kesesuaian Aktual Proses</th>
						<th style="width: 2%; border:1px solid black;">Kelengkapan Point Safety</th>
						<th style="width: 2%; border:1px solid black;">Kesesuaian QC Kouteihyo</th>
						<th style="width: 2%; border:1px solid black;">Operator</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->nama_dokumen}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->no_dokumen}}</td>
						<td style="border:1px solid black; text-align: center;"><?php echo $col->kesesuaian_aktual_proses ?></td>
						<td style="border:1px solid black; text-align: center;"><?php echo $col->kelengkapan_point_safety ?></td>
						<td style="border:1px solid black; text-align: center;"><?php echo $col->kesesuaian_qc_kouteihyo ?></td>
						<td style="border:1px solid black; text-align: center;">{{$col->operator}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/audit_report_activity/print_audit_report_email/'.$activity_list_id.'/'.substr($date,0,7)) }}">See Audit Report Activity Data / Approval Data</a>
		</center>
	</div>
</body>
</html>