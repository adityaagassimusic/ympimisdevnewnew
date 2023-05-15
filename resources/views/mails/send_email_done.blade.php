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
		* {
			font-family: Sans-serif;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">MIRAI APPROVAL SYSTEM (MIRAI 承認システム) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Approval No (承認なし) : {{ $data['appr_sends']->no_transaction }}</h2>
			<h2>Document Title (件名) : <br> {{ $data['appr_sends']->judul }} <br> {{ $data['appr_sends']->jd_japan }}</h2>
		</center>
		<div style="width: 90%; margin: auto;">
		<table style="border:1px solid black; border-collapse: collapse; width: 90%;" align="center">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php
						$nama = explode("/", $data['appr_sends']->nik);
						?>
						<td style="width: 1%; border:1px solid black;">Applicant (申請者)</td>
						<td style="border:1px solid black; text-align: left !important;">{{ $nama[1] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department (課)</td>
						<td style="border:1px solid black; text-align: left !important;">{{ $data['appr_sends']->department }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Document Number (書類番号)</td>
						<td style="border:1px solid black; text-align: left !important;">{{ $data['appr_sends']->no_dokumen }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Document Category (書類カテゴリー)</td>
						<td style="border:1px solid black; text-align: left !important;">{{ $data['appr_sends']->category }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date (作成日)</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($data['appr_sends']->created_at)) ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Note (備考)</td>
						<td style="border:1px solid black; text-align: left !important;">{{ $data['appr_sends']->summary }}</td>
					</tr>	
				</tbody>
			</table>
		@if($data['appr_sends']->remark == 'Send Aplicant Hold & Comment')
		<br>
		<br>
		<center>
			<table style="width: 100%">
				<tr>
					<td>
						<p>
							Permohonan approval anda telah di hold, dengan alasan : <br>
							<?php print_r(nl2br($data['appr_sends']->comment)) ?> <br>
							Periksa kembali di halaman Mirai Approval untuk melakukan upload dokumen terbaru yang sudah diperbarui.<br><br>
						</p>
					</td>
				</tr>
				<tr style="text-align: center">
					<td>
						<a style="background-color: #ccff90; width: 50%;text-decoration: none;color: black;font-size: 20px;"  href="{{ url('index/mirai/approval') }}">&nbsp; Cek Halaman Mirai Approval<br>(チェック) &nbsp;</a>	
					</td>
				</tr>
			</table>
		</center>
		@elseif($data['appr_sends']->remark == 'Rejected')
		<br>
		<br>
		<center>
			<table style="width: 100%">
				<tr>
					<td style="background-color: #FA8072">
						<p>
							Permohonan approval anda telah di reject, dengan alasan : <br>
							<?php print_r(nl2br($data['appr_sends']->comment)) ?> <br><br>
						</p>
					</td>
				</tr>
			</table>
		</center>
		@endif
		<br>
		@if(count($data['appr_approvals']) >= 5)
		<table style="border: 1px solid black; border-collapse: collapse; width: 90%;" align="center">
		@elseif(count($data['appr_approvals']) < 5)
		<table style="border: 1px solid black; border-collapse: collapse; width: 40%;" align="center">
		@endif
			<thead align="center">
				<tr>
					<?php
					for ($i=0; $i < count($data['appr_approvals']); $i++) {
						print_r('<th style="border: 1px solid black; width: 1%;">'.$data['appr_approvals'][$i]->header.'<br>'.$data['appr_approvals'][$i]->remark.'</th>');
					}
					?>
				</tr>
				<tr style="height: 15px;">
					<?php
					for ($i=0; $i < count($data['appr_approvals']); $i++) {
						if ($data['appr_approvals'][$i]->status == 'Approved') {
							if (file_exists(public_path() .'/images/sign/'.$data['appr_approvals'][$i]->approver_id.'.jpg')) {
								?>
								<th><img src="data:image/jpg;base64,{{base64_encode(file_get_contents(public_path('images/sign/'.$data['appr_approvals'][$i]->approver_id.'.jpg')))}}" width="100"><br>{{$data['appr_approvals'][$i]->approved_at}}<br></th>
								<?php
							}
							else if (file_exists(public_path() .'/images/sign/'.$data['appr_approvals'][$i]->approver_id.'.png')) {
								?>
								<th><img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('images/sign/'.$data['appr_approvals'][$i]->approver_id.'.png')))}}" width="100"><br>{{$data['appr_approvals'][$i]->approved_at}}<br></th>
								<?php

							}
							else{
								print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$data['appr_approvals'][$i]->status.'<br>'.$data['appr_approvals'][$i]->approved_at.'</th>');
							}
						}
						else{
							print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$data['appr_approvals'][$i]->status.'<br>'.$data['appr_approvals'][$i]->approved_at.'</th>');
						}
					}
					?>
				</tr>
				<tr>
					<?php
					for ($i=0; $i < count($data['appr_approvals']); $i++) {
						print_r('<th style="border: 1px solid black; width: 1%;">'.$data['appr_approvals'][$i]->approver_name.'</th>');
					}
					?>
				</tr>
			</thead>
		</table>
</div>
</div>
</body>
</html>