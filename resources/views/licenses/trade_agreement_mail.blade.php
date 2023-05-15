<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		tbody>tr>td{
			padding: 5px 5px 5px 5px;
		}
		thead>tr>th{
			padding: 5px 5px 5px 5px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Master Transaction Agreement (取引基本契約書)</span><br>
			<span style="font-weight: bold; font-size: 26px;">REMINDER</span>
		</center>
		<br>
		<div style="margin: auto;">
			<center>
				<span style="font-weight: bold; font-size: 20px; background-color: RGB(255,204,255);">List of Master Transaction Agreement With "OPEN" Status</span>
				<table style="border: 1px solid black; border-collapse: collapse;">
					<thead style="background-color: #605ca8; color: white;">
						<tr>
							<th style="text-align: center; border: 1px solid black; width: 20px;" rowspan="2">#</th>
							<th style="text-align: left; border: 1px solid black; width: 200px;" rowspan="2">Vendor</th>
							<th style="text-align: left; border: 1px solid black; width: 50px;" rowspan="2">Category</th>
							<th style="text-align: left; border: 1px solid black; width: 100px;" rowspan="2">PIC</th>
							<th style="text-align: left; border: 1px solid black; width: 50px;" rowspan="2">Old Ver.</th>
							<th style="text-align: center; border: 1px solid black; width: 80px;" colspan="5">Approval Progress</th>
							<th style="text-align: left; border: 1px solid black; width: 50px;" rowspan="2">Remark</th>
						</tr>
						<tr>
							<th style="text-align: right; background-color: #605ca8; border: 1px solid black; width: 50px;">Sent</th>
							<th style="text-align: right; background-color: #605ca8; border: 1px solid black; width: 50px;">Vendor</th>
							<th style="text-align: right; background-color: #605ca8; border: 1px solid black; width: 50px;">YCJ Pre</th>
							<th style="text-align: right; background-color: #605ca8; border: 1px solid black; width: 50px;">YCJ</th>
							<th style="text-align: right; background-color: #605ca8; border: 1px solid black; width: 50px;">YMPI</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 0; ?>
						@foreach($data['trade_agreements'] as $col)
						<?php $cnt += 1; ?>						
						<tr>
							<td style="text-align: center; font-size: 12px; border:1px solid black;">{{ $cnt }}</td>
							<td style="text-align: left; font-size: 12px; border:1px solid black;">{{ $col->vendor_name }}</td>
							<td style="text-align: left; font-size: 12px; border:1px solid black;">{{ $col->category }}</td>
							<td style="text-align: left; font-size: 12px; border:1px solid black;">{{ $col->pic_id }}<br>{{ $col->pic_name }}</td>
							@if($col->old_version == 0)
							<td style="text-align: left; font-size: 12px; font-weight: bold; color: red; border:1px solid black;">None</td>
							@else
							<td style="text-align: left; font-size: 12px; font-weight: bold; color: green; border:1px solid black;">Exist</td>
							@endif
							<td style="text-align: right; font-size: 12px; border:1px solid black;">{{ $col->sent_at }}</td>
							<td style="text-align: right; font-size: 12px; border:1px solid black;">{{ $col->sign_at }}</td>
							<td style="text-align: right; font-size: 12px; border:1px solid black;">{{ $col->pre_ycj_at }}</td>
							<td style="text-align: right; font-size: 12px; border:1px solid black;">{{ $col->app_ycj_at }}</td>
							<td style="text-align: right; font-size: 12px; border:1px solid black;">{{ $col->app_dir_at }}</td>
							<td style="text-align: left; font-size: 12px; border:1px solid black;">{{ $col->progress }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</center>
		</div>
		<br>
		<br>
		<center>
			Click link below to access Master Transaction Agreement Control
			<br>
			<a href="http://10.109.52.4/mirai/public/index/trade_agreement">Master Transaction Agreement</a>
		</center>
	</div>
</body>
</html>