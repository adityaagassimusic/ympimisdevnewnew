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
			<span style="font-weight: bold; color: purple; font-size: 20px;">SCHEDULE LAPORAN AUDIT IK<br>作業手順書監査</span><br>
			<p style="font-size: 18px;font-weight: bold;">Leader {{$data['datas'][0]->leader}}<br>Periode {{$data['periode']}}</p>
			This is an automatic notification. Please do not reply to this address.<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);color: white">
					<tr>
						<th rowspan="2" style="width: 1%; border:1px solid black;">No.</th>
						<th rowspan="2" style="width: 4%; border:1px solid black;">Dokumen</th>
						<th colspan="{{count($data['month'])}}" style="width: 4%; border:1px solid black;">Schedule</th>
					</tr>
					<tr>
						@foreach($data['month'] as $month)
						<th style="width: 1%; border:1px solid black;background-color: rgb(126,86,134);color: white">{{date('M Y',strtotime($month->month.'-01'))}}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					<?php for($i = 0; $i < count($data['datas']);$i++){ ?>
					<tr>
						<td style="border: 1px solid black;text-align: right;padding-right: 4px;">{{$i+1}}</td>
						<td style="border: 1px solid black">{{$data['datas'][$i]->no_dokumen}} - {{$data['datas'][$i]->nama_dokumen}}</td>
						<?php for($j = 0; $j < count($data['month']);$j++){ 
							$background = 'none';
							if($data['month'][$j]->month == $data['datas'][$i]->month){
								$background = '#c4c4c4';
							} ?>
							<td style="border: 1px solid black;background-color: {{$background}}"></td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="http://10.109.52.4/mirai/public/index/approval/audit_guidance/{{$data['id']}}/{{$data['periode']}}/{{$data['remark']}}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			<br>
			<br>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/audit_guidance/index/{{$data['id']}}">Cek Data</a>
		</center>
	</div>
</body>
</html>