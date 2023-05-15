<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body >
	<style type="text/css">
		@page {
			margin-top: 4%; 
			margin-bottom: 4%;
			margin-left: 4%;
			margin-right: 4%;
			vertical-align: middle;
		}

		.table-outer {
			border: 2px solid black !important;
			width: 100%;
			border-collapse: collapse;
			vertical-align: middle;
			text-align: center;
		}


		.table > tr > td {
			border: 1.5px solid black !important;
			border-collapse: collapse;
			vertical-align: middle;
			text-align: center;
		}

		.title {
			font-size: 26pt;
			font-weight: bold;
			text-align: center;
			vertical-align: middle;
			border-right : 1px solid black !important;
			border-bottom : 1px solid black !important;

		}

		.destination {
			font-size: 20pt;
			font-weight: bold;
			text-align: center;
			vertical-align: middle;
			background-color: #D9D9D9;
			border-top : 1px solid black !important;
			border-bottom: 1px solid black !important;
		}

		.hr {
			border-bottom: 1px solid black !important;
			font-size: 2pt;
		}

		.header-info {
			font-size: 14pt;
			font-weight: bold;
			text-align: left;
			vertical-align: top;
		}

		.header {
			font-size: 12pt;
			font-weight: bold;
		}

		.header-table {
			border-collapse: collapse;
			vertical-align: middle;
			font-size: 12pt;
			font-weight: bold;
			border: 1px solid black !important;
			background-color: #D9D9D9;
			padding-left: 5px;
			padding-right: 5px;
		}		

		.text {
			border-collapse: collapse;
			vertical-align: middle;
			font-size: 12pt;
			border: 1px solid black !important;
			padding-left: 5px;
			padding-right: 5px;
		}		
		
		.qr-field {
			vertical-align: middle;
			text-align: center;
		}

		.qr {
			width: 100px;
			height: 100px;
		}

		.eo_number_sequence {
			font-size: 6pt;
		}


	</style>

	@php
	include public_path(). "/qr_generator/qrlib.php";

	QRcode::png($eo_number_sequence, public_path().'/files/extra_order/qr_code/'.$eo_number_sequence.'.png');

	@endphp

	<table class="table-outer">
		<tr>
			<th colspan="4" rowspan="2" class="title">EXTRA ORDER</th>
			<th colspan="2" class="header" style="vertical-align: middle; text-align: center;">To :</th>
		</tr>

		<tr>
			<th colspan="2" class="destination">{{ $extra_order->destination_shortname }}</th>
		</tr>

		<tr>
			<th colspan="6" class="hr">&nbsp;</th>
		</tr>

		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>

		<tr>
			<td class="header-info" style="width: 5%;">&nbsp;</td>
			<td class="header-info" style="width: 20%;">SHIPPED BY</td>
			<td class="header-info" style="width: 1%;">:</td>
			<td class="header-info" style="width: 49%;">{{ $extra_order_detail->shipment_by }}</td>
			<td class="qr-field" colspan="2" rowspan="5" style="width: 25%;">
				<img class="qr" src="{{ public_path() . '/files/extra_order/qr_code/'.$eo_number_sequence.'.png' }}">
				<p class="eo_number_sequence">{{ $eo_number_sequence }}&nbsp;&nbsp;&nbsp;</p>
			</td>
		</tr>

		<tr>
			<td class="header-info">&nbsp;</td>
			<td class="header-info">ATTENTION</td>
			<td class="header-info">:</td>
			<td class="header-info">{{ $extra_order->attention }}</td>
		</tr>

		<tr>
			<td class="header-info">&nbsp;</td>
			<td class="header-info">DIVISION</td>
			<td class="header-info">:</td>
			<td class="header-info">{{ $extra_order->division }}</td>
		</tr>

		<tr>
			<td class="header-info">&nbsp;</td>
			<td class="header-info">EO NUMBER</td>
			<td class="header-info">:</td>
			<td class="header-info">{{ $extra_order->eo_number }}</td>
		</tr>

		@php



		$po_numbers = json_decode($extra_order->po_number);
		$po_number = '';
		for ($i=0; $i < count($po_numbers); $i++) {

			$file = explode('.', $po_numbers[$i]);
			$filename = $file[0];

			$po_number .= str_replace($extra_order->eo_number . '__', '', $filename);

			if($i != count($po_numbers) -1){
				$po_number += ', ';
			}
		}

		@endphp

		<tr>
			<td class="header-info">&nbsp;</td>
			<td class="header-info">PO NUMBER</td>
			<td class="header-info">:</td>
			<td class="header-info">{{ $po_number }}</td>
		</tr>

		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>

		<tr>
			<th colspan="6">
				<table style="width: 100%;">
					<thead>
						<tr>
							<th style="text-align: center;" class="header-table">No</th>
							<th style="text-align: center;" class="header-table">GMC</th>
							<th style="text-align: center;" class="header-table">DESCRIPTION</th>
							<th style="text-align: center;" class="header-table">QTY</th>
							<th style="text-align: center;" class="header-table">Uom</th>
						</tr>
					</thead>
					<tbody>
						@php $i = 0; @endphp
						@foreach($sequences as $tr)
						<tr>
							<td class="text" style="font-weight: normal; text-align: center; width: 5%;">{{ ++$i }}</td>
							<td class="text" style="font-weight: normal; text-align: center; width: 15%;">{{ $tr->material_number }}</td>
							<td class="text" style="font-weight: normal; text-align: left; width: 60%;">{{ $tr->description }}</td>
							<td class="text" style="font-weight: normal; text-align: center; width: 10%;">{{ $tr->quantity }}</td>
							<td class="text" style="font-weight: normal; text-align: center; width: 10%;">{{ $tr->uom }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</th>
		</tr>

		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>

	</table>
	

</body>
</html>