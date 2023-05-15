<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;"><b>Employee Update Data Notification</b> <br> (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border-color: black; width: 80%;">
				<thead>
					<tr>
						<th style="width: 30%; border:1px solid black; background-color: #d58fff;" colspan="2">Employee Update Information</th>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #e9c4ff;">Employee Id</th>
						<td><?php echo $data['employee_id']; ?></td>
					</tr>
					<tr>
						<th style="border:1px solid black; background-color: #e9c4ff;">Employee Name</th>
						<td><?php echo $data['name']; ?></td>
					</tr>
				</thead>
			</table>
			<br>
			<table style="border-color: black; width: 80%;">
				<thead>
					<tr>
						<th style="width: 30%; border:1px solid black; background-color: #d58fff;" colspan="3">Updated Data</th>
					</tr>
					<tr>
						<th style="width: 20%; border:1px solid black; background-color: #e9c4ff;">Column Data</th>
						<th style="width: 40%; border:1px solid black; background-color: #e9c4ff;">Before</th>
						<th style="width: 40%; border:1px solid black; background-color: #e9c4ff;">After</th>
					</tr>
					<?php foreach ($data['employee'] as $emp) { ?>						
						<tr>
							<td>
								<b><?php echo $emp['name'] ?></b>
							</td>
							<td>
								<?php echo $emp['before'] ?>
							</td>
							<td>
								<?php echo $emp['after'] ?>
							</td>
						</tr>
					<?php } ?>
					<!-- <tr>
						<td>
							<b>Hand Phone</b>
						</td>
						<td>
							085645896741
						</td>
						<td>
							082333311579
						</td>
					</tr>
					<tr>
						<td>
							<b>Status Perkawinan</b>
						</td>
						<td>
							LAJANG
						</td>
						<td>
							KAWIN
						</td>
					</tr>
					<tr>
						<td>
							<b>Suami/Istri</b>
						</td>
						<td>
							Nama : - <br>
							Jk : - <br>
							Tempat Lahir : - <br>
							Tanggal Lahir : - <br>
							Pekerjaan : - <br>
						</td>
						<td>
							Nama : EKA DESY WAHYUNINGSIH <br>
							Jk : P <br>
							Tempat Lahir : PASURUAN <br>
							Tanggal Lahir : 1998-10-17 <br>
							Pekerjaan : Ibu Rumah Tangga <br>
						</td>
					</tr> -->

				</table>
				<br>
			<!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
				<a href="http://10.109.52.4/mirai/public/index/qnaHR">HR Q&A</a><br> -->
			</center>
		</div>
	</body>
	</html>