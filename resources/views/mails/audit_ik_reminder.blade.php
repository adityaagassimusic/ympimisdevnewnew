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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 20px;">REMINDER PENANGANAN AUDIT IK</span><br>
			This is an automatic notification. Please do not reply to this address.<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);color: white">
					<tr>
						<th style="width: 1%; border:1px solid black;">No.</th>
						<th style="width: 4%; border:1px solid black;">Leader</th>
						<th style="width: 4%; border:1px solid black;">Category</th>
						@foreach($data['month'] as $month)
						<th style="width: 1%; border:1px solid black;">{{date('M Y',strtotime($month.'-01'))}}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					<?php for($i = 0; $i < count($data['leader']);$i++){ ?>
					<tr>
						<td rowspan="4" style="border: 1px solid black;text-align: right;padding-right: 4px;">{{$i+1}}</td>
						<td rowspan="4" style="border: 1px solid black">{{$data['leader'][$i]}}</td>
						<td style="border: 1px solid black">Revisi IK</td>
						<?php for($j = 0; $j < count($data['month']);$j++){ ?>
							<?php $ada = ''; ?>
							<?php $color = 'none'; ?>
							<?php for($k = 0; $k < count($data['audit']);$k++){ 
								if($data['audit'][$k]->month == $data['month'][$j] && $data['audit'][$k]->username == explode(' - ',$data['leader'][$i])[0] && $data['audit'][$k]->revisi_ik != 0){
									$ada = $data['audit'][$k]->revisi_ik;
									$color = '#c9c9c9';
								}
							} ?>
							<td style="border: 1px solid black;text-align: right;padding-right: 7px;background-color: {{$color}}">{{$ada}}</td>
						<?php } ?>
					</tr>
					<tr>
						<td style="border: 1px solid black">Repair Jig</td>
						<?php for($j = 0; $j < count($data['month']);$j++){ ?>
							<?php $ada = ''; ?>
							<?php $color = 'none'; ?>
							<?php for($k = 0; $k < count($data['audit']);$k++){ 
								if($data['audit'][$k]->month == $data['month'][$j] && $data['audit'][$k]->username == explode(' - ',$data['leader'][$i])[0] && $data['audit'][$k]->revisi_jig != 0){
									$ada = $data['audit'][$k]->revisi_jig;
									$color = '#c9c9c9';
								}
							} ?>
							<td style="border: 1px solid black;text-align: right;padding-right: 7px;background-color: {{$color}}">{{$ada}}</td>
						<?php } ?>
					</tr>
					
					<tr>
						<td style="border: 1px solid black">Revisi QC Koteihyo</td>
						<?php for($j = 0; $j < count($data['month']);$j++){ ?>
							<?php $ada = ''; ?>
							<?php $color = 'none'; ?>
							<?php for($k = 0; $k < count($data['audit']);$k++){ 
								if($data['audit'][$k]->month == $data['month'][$j] && $data['audit'][$k]->username == explode(' - ',$data['leader'][$i])[0] && $data['audit'][$k]->revisi_qc_koteihyo != 0){
									$ada = $data['audit'][$k]->revisi_qc_koteihyo;
									$color = '#c9c9c9';
								}
							} ?>
							<td style="border: 1px solid black;text-align: right;padding-right: 7px;background-color: {{$color}}">{{$ada}}</td>
						<?php } ?>
					</tr>
					<tr>
						<td style="border: 1px solid black">IK Obsolete</td>
						<?php for($j = 0; $j < count($data['month']);$j++){ ?>
							<?php $ada = ''; ?>
							<?php $color = 'none'; ?>
							<?php for($k = 0; $k < count($data['audit']);$k++){ 
								if($data['audit'][$k]->month == $data['month'][$j] && $data['audit'][$k]->username == explode(' - ',$data['leader'][$i])[0] && $data['audit'][$k]->ik_obsolete != 0){
									$ada = $data['audit'][$k]->ik_obsolete;
									$color = '#c9c9c9';
								}
							} ?>
							<td style="border: 1px solid black;text-align: right;padding-right: 7px;background-color: {{$color}}">{{$ada}}</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/audit_ik_monitoring">Audit IK Monitoring</a>
		</center>
	</div>
</body>
</html>