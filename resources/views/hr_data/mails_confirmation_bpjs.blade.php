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
			<p style="font-size: 18px;">Persetujuan Pengajuan BPJSKES<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
		</center>
		<div style="width: 90%; margin: auto;">
			<!-- <p>Saya, yang melakukan pengajuan untuk anggota keluarga saya </p> -->
			<table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">NIK</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['karyawan']->employee_id }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Nama</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['karyawan']->name }}</td>
                    </tr>
                </tbody>
            </table>
            <p>Mengajukan anggota keluarga saya di bawah ini untuk di masukkan kedalam tagihan BPJSKES dari gaji saya.</p>
			<table style="border:1px solid black; border-collapse: collapse; width: 90%;" align="center">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th width="10%" style="border:1px solid black; text-align: center; font-size: 12px">No</th>
						<th width="20%" style="border:1px solid black; text-align: center; font-size: 12px">Nama</th>
						<th width="40%" style="border:1px solid black; text-align: center; font-size: 12px">No BPJS</th>
						<th width="20%" style="border:1px solid black; text-align: center; font-size: 12px">Hubungan Keluarga</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['keluarga']); $i++) { 
						$no = $i+1;
						print_r('<tr><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$no++.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$data['keluarga'][$i]->name.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$data['keluarga'][$i]->bpjs_number.'</td><td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">'.$data['keluarga'][$i]->remark.'</td></tr>');
					}
					?> 
				</tbody>
			</table>
			<br>
			<br>
			<center>
				Apakah anda menyetujui permintaan ini?
				<br>
				<table style="width: 100%">
					<tr>
						<th style="width: 10%; font-weight: bold; color: black; text-align: center">
							<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"  href="{{ url('confirmation/pengajuan/anggota/bpjs?status=Approved&id_reg=' . $data['keluarga'][0]->id_reg) }}">Approve<br>(承認)</a>
						</th>
						<th style="width: 10%; font-weight: bold; color: black; text-align: center">
							<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('confirmation/pengajuan/anggota/bpjs?status=Rejected&id_reg=' . $data['keluarga'][0]->id_reg) }}">Reject<br>(却下)</a>
						</th>
					</tr>
				</table>
			</center>
		</div>
	</div>
</body>
</html>