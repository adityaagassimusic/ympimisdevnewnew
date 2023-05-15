<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table, td, th {
			border: 1px solid black;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.column-table {
			flex: 50%;
			padding: 5px;
		}
		.row-table {
			display: flex;
			margin-left:-5px;
			margin-right:-5px;
		}

	</style>
</head>
<body style="font-family: calibri;">
	<div>
		<center>
			<span style="font-weight: bold; font-size: 20px;">
				{{ date('Y', strtotime($safety_ridings[0]->period)) }}年 {{ date('m', strtotime($safety_ridings[0]->period)) }}月 Catatan Record Penerapan 『Janji Safety Riding』
			</span>
		</center>
		<br>
		<table style="border: 0px;">
			<tr>
				<th style="text-align: left; border: 0px;">
					<span>
						① Perkirakan waktu untuk tiba dengan selamat di tempat tujuan. (Mari berangkat kerja lebih awal.)
						<br>
						② Marilah patuhi aturan berlalu lintas demi orang-orang tercinta kita.
					</span>
				</th>
				<th style="text-align: right; border: 0px;">
					<span>
						No Dok. : YMPI/STD/FK3/054<br>
						Rev		: 00<br>
						Tanggal	: 01 April 2015
					</span>
				</th>
			</tr>
		</table>
		<br>
		<br>
		<div class="row-table">
			<div class="column-table">
				<table class="table table-hover table-bordered" style="margin-bottom: 20px; width: 100%;">
					<thead>
						<tr>
							<th rowspan="3" style="width: 3%;">{{ $safety_ridings[0]->department }}</th>
							<th colspan="2" style="width: 1%;">Sebelum Mulai</th>
						</tr>
						<tr>
							<th style="width: 1%;">② Manager</th>
							<th style="width: 1%;">① Chief</th>
						</tr>
						<tr>
							<th style="height: 40px; width: 1%;">{{ $mb_name }}<br>{{ $mb_at }}</th>
							<th style="height: 40px; width: 1%;">{{ $cb_name }}<br>{{ $cb_at }}</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="column-table">
				<table class="table table-hover table-bordered pull-right" style="margin-bottom: 20px; width: 50%; float: right;">
					<thead>
						<tr>
							<th colspan="2" style="width: 1%;">Sesudah Selesai</th>
						</tr>
						<tr>
							<th style="width: 1%;">④ Manager</th>
							<th style="width: 1%;">③ Chief</th>
						</tr>
						<tr>
							<th style="height: 40px; width: 1%;">{{ $ma_name }}<br>{{ $ma_at }}</th>
							<th style="height: 40px; width: 1%;">{{ $ca_name }}<br>{{ $ca_at }}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<br>
	</div>
	<table>
		<thead style="height: 50px;">
			<tr>
				<th>#</th>
				<th>Nama</th>
				<th>Janji Safety Riding</th>
				@foreach($weekly_calendars as $weekly_calendar)
				@if($weekly_calendar->remark == 'H')
				<th style="background-color: rgba(80,80,80,0.3)">{{ date('d', strtotime($weekly_calendar->week_date)) }}</th>
				@else
				<th>{{ date('d', strtotime($weekly_calendar->week_date)) }}</th>
				@endif
				@endforeach
			</tr>
		</tead>
		<tbody>
			<?php
			$count = 1;
			$pics = array();?>
			@foreach($safety_ridings as $safety_riding)
			@if(!in_array($safety_riding->employee_id, $pics))
			<tr style="height: 45px;">
				<td style="padding-right: 2px; padding-left: 2px; width: 0.4%; text-align: center;">{{ $count }}</td>
				<td style="padding-right: 2px; padding-left: 2px; width: 7%;">{{ $safety_riding->employee_name }}</td>
				<td style="padding-right: 2px; padding-left: 2px; width: 12%;">{{ $safety_riding->safety_riding }}</td>
				@foreach($weekly_calendars as $weekly_calendar)
				<?php
				$ins = false;?>
				@foreach($safety_ridings as $safety_riding_record)
				@if($weekly_calendar->week_date == $safety_riding_record->due_date && $safety_riding->employee_id == $safety_riding_record->employee_id)
				@if($safety_riding_record->remark == 'maru')
				<td style="width: 0.4%; text-align: center;">&#9711;</td>
				@else
				<td style="width: 0.4%; text-align: center;">&#9747;</td>
				@endif
				<?php
				$ins = true;?>
				@endif
				@endforeach
				@if($ins == false)
				<td style="width: 0.4%; text-align: center;"></td>
				@endif
				@endforeach
			</tr>
			<?php
			array_push($pics, $safety_riding->employee_id);
			$count += 1;?>
			@endif
			@endforeach
		</tbody>
	</table>
</body>
</html>