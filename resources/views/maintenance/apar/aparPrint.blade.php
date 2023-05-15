<!DOCTYPE html>
<html>
<head>
	<title>APAR PRINT</title>
	<style type="text/css">
		@font-face {
			font-family: 'Bebas2';
			font-style: normal;
			font-weight: normal;
			src: url(<?php echo public_path() . '/fonts/Bebas-Regular2.ttf' ?> ) format('truetype');
		}

		body {
			font-family: 'Bebas2';
		}

		b {
			font-family: 'Bebas2' !important;
		}

		.cropped {
			width: 100px;
			height: 100px;
			background-position: center center;
			background-repeat: no-repeat;
			display: block;
			margin-left: auto;
			margin-right: auto;
		}
		
		@page { margin: 0px; }
	</style>
</head>
<?php 
include public_path(). "/qr_generator/qrlib.php"; 

QRcode::png($data['apar_code']."/".$data['remark'], public_path().'/apar-qr.png');
?>
<body>

	<table width="100%">
		<tr>
			<td colspan="2" style="text-align: center; color: white; background-color: black;">
				<b>{{$data['apar_code']}}</b> <br>
				{{$data['apar_name']}}
			</td>
		</tr>
		<tr style="line-height: 15px">
			<td colspan="2"><center>Pengecekan 2 bulanan</center></td>
		</tr>
		<tr style="line-height: 12px">
			<td style="width: 45%"><img src="{{ public_path() . '/apar-qr.png' }}" class="cropped"></td>
			<td style="text-align: left">
				Exp. {{$data['exp_date']}} <br>
				Last Check : <br> {{$data['last_check']}} <br>  {{$data['last_check2']}}<br>({{$data['status']}})
			</td>
		</tr>
	</table>
</body>
</html>