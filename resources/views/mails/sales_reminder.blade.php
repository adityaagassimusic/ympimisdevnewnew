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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<br>
			<br>
			<table style="border: 1px solid black; border-collapse: collapse;" width="50%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="border:1px solid black; width: 15%; font-size: 11px; padding: 0px 0px 0px 0px;">Checksheet</th>
						<th style="border:1px solid black; width: 20%; font-size: 11px; padding: 0px 0px 0px 0px;">Invoice No.</th>
						<th style="border:1px solid black; width: 20%; font-size: 11px; padding: 0px 0px 0px 0px;">Destination</th>
						<th style="border:1px solid black; width: 15%; font-size: 11px; padding: 0px 0px 0px 0px;">Ship by</th>
						<th style="border:1px solid black; width: 15%; font-size: 11px; padding: 0px 0px 0px 0px;">St Date</th>
						<th style="border:1px solid black; width: 15%; font-size: 11px; padding: 0px 0px 0px 0px;">Etd Date</th>
					</tr>
				</thead>
				<tbody>
					@php
					foreach ($data['outstanding'] as $row){
						$css = "border: 1px solid black; padding: 0px 5px 0px 5px; font-size:11px;";
						print_r('<tr>');
						print_r('<td style="'.$css.'text-align:center;">'.$row->container_id.'</td>');
						print_r('<td style="'.$css.'text-align:left;">'.$row->invoice.'</td>');
						print_r('<td style="'.$css.'text-align:left;">'.$row->destination.'</td>');
						print_r('<td style="'.$css.'text-align:center;">'.$row->shipment_condition_name.'</td>');
						print_r('<td style="'.$css.'text-align:center;">'.$row->Stuffing_date.'</td>');
						print_r('<td style="'.$css.'text-align:center;">'.$row->etd_sub.'</td>');
						print_r('</tr>');
					}
					@endphp
				</tbody>
			</table>
			<br>
			<br>
			<a href="http://10.109.52.4/mirai/public/index/flo_view/lading">&#10148; Update Shipment On Board</a>
		</center>
	</div>
</body>
</html>