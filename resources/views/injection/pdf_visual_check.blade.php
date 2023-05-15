<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table, td, th {
			border: 1px solid black;
			text-align: center;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

	</style>
</head>
<body style="font-family: calibri;">
	
	<table>
		<thead style="height: 50px;">
			<tr>
				<th colspan="{{count($point_check)+6}}" style="padding: 10px;font-size: 25px">PENGECEKAN HASIL INJEKSI {{strtoupper(explode('_',$point_check[0]->part_type)[0])}} {{strtoupper($percentage[0]->part_type)}}</th>
			</tr>
			<tr>
				<th rowspan="2" style="font-size: 12px;padding:5px">Tanggal</th>
				<th rowspan="2" style="font-size: 12px;padding:5px">Mesin</th>
				<th rowspan="2" style="font-size: 12px;padding:5px">Produk</th>
				<th rowspan="2" style="font-size: 12px;padding:5px">Jam</th>
				<th rowspan="2" style="font-size: 12px;padding:5px">Cavity</th>
				<th colspan="{{count($point_check)+1}}">Point Cek</th>
			</tr>
			<tr>
				@foreach($point_check as $point_check)
				<th style="font-size: 13px;padding: 5px">{{$point_check->point_check_index}}. {{$point_check->point_check_name}}</th>
				@endforeach
				<th style="font-size: 13px;padding: 5px">Note</th>
			</tr>
		</tead>
		<tbody style="font-size: 12px;">
			@foreach($percentage as $percentage)
			<?php $cav_detail = explode(',', $percentage->cav_detail) ?>
			<?php $result_all = explode('_', $percentage->result_all) ?>
			<tr>
				<td rowspan="{{count($cav_detail)+1}}">{{$percentage->created}}</td>
				<td rowspan="{{count($cav_detail)+1}}">{{$percentage->machine}}</td>
				<td rowspan="{{count($cav_detail)+1}}">{{$percentage->material_description}}</td>
				<td rowspan="{{count($cav_detail)+1}}">{{$percentage->hour_check}}</td>
			</tr>
				@foreach($cav_detail as $cav_detail)
				<tr>
				<td>{{$cav_detail}}</td>
				for()
				</tr>
				@endforeach
			@endforeach
		</tbody>
	</table>
	
</body>
</html>