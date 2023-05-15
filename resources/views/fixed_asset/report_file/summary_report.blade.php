<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		td {
			padding: 0px;
			font-size: 12px;
		}
		th {
			font-size: 13px;
			padding: 1px;
			vertical-align: middle;
		}
		.border1 {
			border: 1px solid black;
		}
		.bg_bold {
			color: blue;
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<table width="100%">
				<tr>
					<td width="70%" style="font-size: 13px">
						<b>PT.YAMAHA MUSICAL PRODUCTS INDONESIA</b> <br><br>
						<b>RESULT AUDIT FIXED ASSET</b> <br>
						<b>PERIODE {{ strtoupper($periode) }}</b> <br><br>
					</td>
				</tr>
			</table>

			<table class="table table-hover table-bordered table-striped" id="tableSummary" style="width: 100%">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr style="color: white">
						<th style="color:white;width: 1%;" rowspan="2">No</th>
						<th style="color:white;width: 25%; text-align: center;" rowspan="2">SECTION</th>
						<th style="color:white;width: 7%; text-align: center;" rowspan="2">QTY FA</th>
						<th style="color:white;width: 10%; text-align: center;" colspan="2">AUDIT RESULT</th>
						<th style="color:white;width: 10%; text-align: center;" colspan="5">CONDITION</th>
					</tr>
					<tr>
						<th style="color:white;width: 10%; text-align: center;">AVAILABLE</th>
						<th style="color:white;width: 10%; text-align: center;">NOT AVAILABLE</th>
						<th style="color:white;width: 10%; text-align: center;">BROKEN</th>
						<th style="color:white;width: 10%; text-align: center;">NOT USE</th>
						<th style="color:white;width: 10%; text-align: center;">LABEL BROKEN</th>
						<th style="color:white;width: 10%; text-align: center;">MAP NOT UPDATE</th>
						<th style="color:white;width: 10%; text-align: center;">IMAGE NOT UPDATE</th>
					</tr>
				</thead>
				<tbody id="tableDetailSummary">
					<?php 
					$tot_asset = 0;
					$sum_ada = 0;
					$sum_tidak_ada = 0;
					$sum_rusak = 0;
					$sum_tidak_digunakan = 0;
					$sum_label = 0;
					$sum_tidak_map = 0;
					$sum_tidak_foto = 0;

					$num = 1;

					foreach ($datas as $dt) {
						echo "<tr>";
						echo "<td style='text-align: right'>".$num."</td>";
						echo "<td>".$dt->asset_section."</td>";
						echo "<td style='text-align: right'>".$dt->total_asset."</td>";
						echo "<td style='text-align: right'>".$dt->ada_asset."</td>";

						if ($dt->tidak_ada_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->tidak_ada_asset."</td>";
						}

						if ($dt->rusak_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->rusak_asset."</td>";
						}

						if ($dt->tidak_digunakan_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->tidak_digunakan_asset."</td>";
						}

						if ($dt->label_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->label_asset."</td>";
						}

						if ($dt->tidak_map_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->tidak_map_asset."</td>";
						}

						if ($dt->tidak_foto_asset == '0') {
							echo "<td style='text-align: right'> - </td>";
						} else {
							echo "<td style='text-align: right'>".$dt->tidak_foto_asset."</td>";
						}

						echo "</tr>";

						$tot_asset += (int) $dt->total_asset;
						$sum_ada += (int) $dt->ada_asset;
						$sum_tidak_ada += (int) $dt->tidak_ada_asset;
						$sum_rusak += (int) $dt->rusak_asset;
						$sum_tidak_digunakan += (int) $dt->tidak_digunakan_asset;
						$sum_label += (int) $dt->label_asset;
						$sum_tidak_map += (int) $dt->tidak_map_asset;
						$sum_tidak_foto += (int) $dt->tidak_foto_asset;

						$num ++;
					}

					echo "<tr style='background-color: rgba(126,86,134,.3);'>";
					echo "<td></td>";
					echo "<td><b>Total</b></td>";
					echo "<td style='text-align: right'><b>".$tot_asset."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_ada."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_tidak_ada."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_rusak."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_tidak_digunakan."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_label."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_tidak_map."</b></td>";
					echo "<td style='text-align: right'><b>".$sum_tidak_foto."</b></td>";
					echo "</tr>";
					?>
				</tbody>
			</table>
			<br>
			<table style="width: 50%; border: 1px solid black" border="1">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">PREPARED</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">CHECKED</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">APPROVED</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">APPROVED</th>
					</tr>
					<tr>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">STAFF</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">MANAGER</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">FINANCE DIRECTOR</th>
						<th style="color:white;width: 1%; text-align: center; font-size: 13px">PRESIDENT DIRECTOR</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="color:white;width: 1%; text-align: center; font-size: 10px;">
							<?php if ($sign->prepared_at) {
								echo $sign->prepared_at;
							} else {
								echo '&nbsp;<br>';
							}?>
						</td>
						<td style="color:white;width: 1%; text-align: center; font-size: 10px;">
							<?php if ($sign->acc_manager_at) {
								echo $sign->acc_manager_at;
							} else {
								echo '&nbsp;<br>';
							}?>
						</td>
						<td style="color:white;width: 1%; text-align: center; font-size: 10px;">
							<?php if ($sign->finance_director_at) {
								echo $sign->finance_director_at;
							} else {
								echo '&nbsp;<br>';
							}?>
						</td>
						<td style="color:white;width: 1%; text-align: center; font-size: 10px;">
							<?php if ($sign->president_director_at) {
								echo $sign->president_director_at;
							} else {
								echo '&nbsp;<br>';
							}?>
						</td>
					</tr>
					<tr style='background-color: rgba(126,86,134,.3);'>
						<th style="color:black;width: 1%; text-align: center; font-size: 10px">{{ explode('/',$sign->prepared_by)[1] }}</th>
						<th style="color:black;width: 1%; text-align: center; font-size: 10px">{{ explode('/',$sign->acc_manager)[1] }}</th>
						<th style="color:black;width: 1%; text-align: center; font-size: 10px">{{ explode('/',$sign->finance_director)[1] }}</th>
						<th style="color:black;width: 1%; text-align: center; font-size: 10px">{{ explode('/',$sign->president_director)[1] }}</th>
					</tr>
				</tbody>
			</table>

		</center>
	</div>
</body>
</html>