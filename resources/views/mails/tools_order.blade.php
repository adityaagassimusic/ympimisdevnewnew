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
			<p style="font-weight: bold; color: purple; font-size: 24px;">Tools Need Order</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Rack Code</th>
						<th style="width: 2%; border:1px solid black;">Tools</th>
						<th style="width: 2%; border:1px solid black;">Group</th>
						<th style="width: 2%; border:1px solid black;">Category</th>
						<th style="width: 2%; border:1px solid black;">Remark</th>
						<th style="width: 2%; border:1px solid black;">Stock Kanban</th>
						<th style="width: 2%; border:1px solid black;">Minimum Kanban</th>
						<!-- <th style="width: 2%; border:1px solid black;">Qty Ordere</th> -->
					</tr>
				</thead>
				<tbody>
						<tr>
							<td style="border:1px solid black; text-align: center;">{{ $data->rack_code }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->item_code }} {{ $data->description }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->group }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->category }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->remark }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->stock_kanban }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->need_kanban }}</td>
							<!-- <td style="border:1px solid black; text-align: center;">{{ $data->quantity_order }}</td> -->
						</tr>
				</tbody>
			</table>	
		</center>
	</div>
</body>
</html>