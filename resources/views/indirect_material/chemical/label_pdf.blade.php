<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		.cropped {
			width: 220px;
			height: 200px;
			vertical-align: middle;
			text-align: center;
		}

		.cropped_small {
			width: 75px;
			height: 75px;
			vertical-align: middle;
			text-align: center;
		}

		table tr td{
			border: 3px solid black;
			border-collapse: collapse;
			vertical-align: middle;
		}

		@page {
			margin: 50px; 
			vertical-align: middle;
		}

		.page-break {
			page-break-after: always;
		}


	</style>
</head>

<body style="text-transform: uppercase; color: #000;">


	@php
	include public_path(). "/qr_generator/qrlib.php";

	for ($i=0; $i < count($data); $i++) { 	

		@endphp

		@php
		QRcode::png($data[$i]->qr_code, public_path().'/'.$data[$i]->qr_code.'.png');
		@endphp


		@php
		if($data[$i]->label == 'BESAR'){
			@endphp
			<table style="width: 100%; margin-top: 5%;">
				<tbody style="font-weight: bold;">
					<tr>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px; width: 20%">GMC</td>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 50px; width: 50%">{{ $data[$i]->material_number }}</td>
						<td style="vertical-align: middle; font-size: 30px; width: 30%; text-align: center;">
							{{ $data[$i]->month }}
						</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px;">Desc.</td>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 26px;">{{ $data[$i]->material_description }}</td>
						<td rowspan="3" style="text-align: center; vertical-align: middle;">
							<img src="{{ public_path() . '/'.$data[$i]->qr_code.'.png' }}" class="cropped">
							<span style="font-size: 10px;">{{ $data[$i]->qr_code }}</span>
						</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Masuk</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->masuk }}</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Mfg</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->mfg }}</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Exp</td>
						@if(is_null($data[$i]->exp))
						<td style="padding: 0px 5px 0px 5px; font-size: 15px; vertical-align: bottom;">{{ $data[$i]->expired }} hari setelah tutup dibuka</td>
						@else
						<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->exp }}</td>
						@endif
						<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle; text-align: center;">Lisensi :<br>{{ $data[$i]->license }}</td>
					</tr>
				</tbody>
			</table>
			@php
		}else{	
			@endphp
			<table style="border: 2px solid black; border-top: 0px; border-bottom: 0px; width: 55%;">
				<tbody style="font-weight: bold;">
					<tr>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 12px; width: 20%">GMC</td>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 20px; width: 50%">{{ $data[$i]->material_number }}</td>
						<td style="vertical-align: middle; font-size: 11px; width: 30%; text-align: center;">
							{{ $data[$i]->month }}
						</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 12px;">Desc.</td>
						<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 14px;">{{ $data[$i]->material_description }}</td>
						<td rowspan="3" style="text-align: center; vertical-align: middle;">
							<img src="{{ public_path() . '/'.$data[$i]->qr_code.'.png' }}" class="cropped_small" style="margin-top: 3px;">
							<span style="font-size: 10px;">{{ $data[$i]->qr_code }}</span>
						</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 12px; vertical-align: middle;">Tgl Masuk</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 20px; vertical-align: middle;">{{ $data[$i]->masuk }}</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 12px; vertical-align: middle;">Tgl Mfg</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 20px; vertical-align: middle;">{{ $data[$i]->mfg_date }}</td>
					</tr>
					<tr>
						<td style="padding: 0px 5px 0px 5px; font-size: 12px; vertical-align: middle;">Tgl Exp</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 20px; vertical-align: middle;">{{ $data[$i]->exp }}</td>
						<td style="padding: 0px 5px 0px 5px; font-size: 8px; vertical-align: middle; text-align: center;">Lisensi :<br>{{ $data[$i]->license }}</td>
					</tr>

				</tbody>
			</table>
			@php
		}	
		@endphp

		@if($i != (count($data)-1))

		<div class="page-break"></div>

		@endif

		@php
	}	
	@endphp

</body>
</html>