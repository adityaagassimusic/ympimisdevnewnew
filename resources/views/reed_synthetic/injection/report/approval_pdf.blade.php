<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<title>First Approval {{ $approval->material_description }}</title>
</head>
<body >
	<style type="text/css">
		table tr td{
			border-collapse: collapse;
			vertical-align: middle;
		}

		table.table > tbody > tr > td {
			padding-top: 0px;
			padding-bottom: 0px;
			border: 1px solid black;
			font-size: 10px;
		}

		@page {
			margin-top: 5%; 
			margin-bottom: 0px; 
			vertical-align: middle;
		}

	</style>

	{{-- <img id="yamaha-logo" src="{{ public_path() . '/files/reed/yamaha.png' }}"> --}}

	@php

	$desc = explode(' ', $approval->material_description);
	$product = $desc[0];

	@endphp

	<table class="table table-bordered" style="margin-bottom: 0px !important;">
		<tbody>
			<tr>
				<td colspan="6" class="">PT. Yamaha Musical Products Indonesia</td>
				<td rowspan="3" colspan="15" class="">APPROVAL FIRST SHOTS INJECTION SYNTHETIC REED</td>
				<td class="">No Dok.</td>
				<td colspan="2" class="">: YMPI/PE/FM/002</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="4" class=""></td>
				<td colspan="3" class="">Departemen</td>
				<td colspan="1" class="">PE</td>
				<td class="">Revisi</td>
				<td colspan="2" class="">: 18</td>
			</tr>
			<tr>
				<td colspan="3" class="">Sub Section</td>
				<td colspan="1" class="">PE</td>
				<td class="">Tanggal</td>
				<td colspan="2" class="">: 01-12-2019</td>
			</tr>
			<tr>
				<td colspan="3" class="">Tanggal</td>
				<td colspan="1" class=""></td>
				<td colspan="5" rowspan="2" class="">TROUBLESHOOTING</td>
				<td colspan="5" rowspan="2" class="">MOLD CHANGE</td>
				<td colspan="5" rowspan="2" class="">COLOR CHANGE</td>
				<td class="">Hal</td>
				<td colspan="2" class="">: </td>
			</tr>
			<tr>
				<td colspan="3" class="">Subject</td>
				<td colspan="1" class="">Check</td>
				<td class=""></td>
				<td colspan="2" class=""></td>
			</tr>
			<tr>
				<td colspan="24"></td>
			</tr>
			<tr>
				<td colspan="24"></td>
			</tr>
			<tr>
				<td colspan="3">Produk</td>
				<td colspan="4"></td>
				<td colspan="2">Mold</td>
				<td colspan="4"></td>
				<td colspan="2">Note</td>
				<td colspan="9"></td>
			</tr>
			<tr>
				<td colspan="3" rowspan="5"></td>
				<td colspan="2">Type</td>
				<td colspan="2">Color</td>
				<td colspan="2">Mold</td>
				<td colspan="1" rowspan="5"></td>
				<td colspan="2">INJECTION MACHINE</td>
				<td colspan="2">NO </td>
				<td colspan="2">Mold change</td>
				<td colspan="9">1. Setiap ganti Molding</td>
			</tr>

			
		</tbody>
	</table>
</body>
</html>