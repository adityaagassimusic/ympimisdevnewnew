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
			<p style="font-size: 18px;">MIRAI APPROVAL SYSTEM (MIRAI 承認システム)</p>

			<!-- <br>(Last Update: {{ date('d-M-Y H:i:s') }} -->

			<!-- This is an automatic notification. Please do not reply to this address. -->
		</center>
		<!-- <br> -->
		<div style="width: 99%; margin: auto;">
			<table style="border-collapse: collapse; width: 100%;" align="center">
				<tr>
					<td style="width: 60%">
						 <iframe src="{{ url('adagio/ADG'.$data->no_transaction.'.pdf') }}" width="100%" height="800px"></iframe>
					</td>
					<td style="width: 40%">
						<!-- <center>
							<h2>Approval No (承認なし) : {{ $data->no_transaction }}</h2>
							<h2>Document Title (件名) : <br> {{ $data->description }} <br> {{ $data->jd_japan }}</h2>
						</center> -->
						<table style="border:1px solid black; border-collapse: collapse; width: 100%; padding-top: 0px" align="center">
							<thead style="background-color: rgb(126,86,134);">
								<tr>
									<th style="width: 2%; border:1px solid black;">Point</th>
									<th style="width: 4%; border:1px solid black;">Content</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Judul Dokumen (資料名)</td>
									<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->judul }}</td>
								</tr>

								<tr>
									<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">No Approval (承認番号)</td>
									<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->no_transaction }}</td>
								</tr>
								<tr>
									<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">No Dokumen (資料番号)</td>
									<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->no_dokumen }}</td>
								</tr>
							</tr>              
							<?php
							$identitas = explode("/",$data->nik);
							?> 
							<tr>
								<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Pembuat (作成者)</td>
								<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $identitas[0] }} - {{ $identitas[1] }}</td>
							</tr>
							<tr>
								<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px;">Department (課)</td>
								<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->department }}</td>
							</tr>
							<tr>
								<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Tanggal Pembuatan (作成日)</td>
								<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->created_at }}</td>
							</tr>
							<tr>
								<td style="font-size: 15px; font-weight: bold; border: 1px solid black; padding-left: 20px">Catatan (備考)</td>
								<td style="font-size: 15px; border: 1px solid black; padding-left: 20px">{{ $data->summary }}</td>
							</tr>
								<!-- <tr>
									<?php
									$nama = explode("/", $data->nik);
									?>
									<td style="width: 1%; border:1px solid black;">Applicant (申請者)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $nama[1] }}</td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Department (課)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $data->department }}</td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Document Number (書類番号)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $data->no_dokumen }}</td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Document Category (書類カテゴリー)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $data->judul }}</td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Submission Date (作成日)</td>
									<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($data->created_at)) ?></td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Note (備考)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $data->summary }}</td>
								</tr>
								<tr>
									<td style="width: 1%; border:1px solid black;">Attach File (???)</td>
									<td style="border:1px solid black; text-align: left !important;">{{ $data->no_transaction }}</td>
								</tr> -->
								@if($data->answer != null)
								<tr>
									<td style="width: 1%; border:1px solid black; background-color: yellow">Answer (備考)</td>
									<td style="border:1px solid black; text-align: left !important; background-color: yellow">File {{ $data->answer }}</td>
								</tr>
								@endif		
							</tbody>
						</table>
						<br>
						<br>
						<center>
							Do you want to Approve this Mirai Approval Request?
							<br>
							<table style="width: 100%">
								<tr>
									<th style="width: 10%; font-weight: bold; color: black;">
										<!-- <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"  href="{{ url('adagio/verivikasi/'.$data->no_transaction.'/'.$approver_id) }}">&nbsp; Approve(承認) &nbsp;</a><br><br> -->

										<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"  href="http://10.109.52.4/mirai/public/verivikasi/email/{{ $data->no_transaction }}">&nbsp; Approve(承認) &nbsp;</a><br><br>

										<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="http://10.109.52.4/mirai/public/hold/email/{{ $data->no_transaction }}">&nbsp;&nbsp;&nbsp; Hold & Comment(ホールド＆コメント) &nbsp;&nbsp;&nbsp;</a><br><br>

										<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;" href="http://10.109.52.4/mirai/public/adagio/rejected/{{ $data->no_transaction }}/{{ $approver_id }}">&nbsp;&nbsp;&nbsp; Reject(却下) &nbsp;&nbsp;&nbsp;</a>

									</th>
								</tr>
							</table>
						</center>
						<br>
						<br>
						<br>
		@if(count($appr_approvals) >= 5)
		<table style="border: 1px solid black; border-collapse: collapse; width: 90%;" align="center">
		@elseif(count($appr_approvals) < 5)
		<table style="border: 1px solid black; border-collapse: collapse; width: 40%;" align="center">
		@endif
			<thead align="center">
				<tr>
					<?php
					for ($i=0; $i < count($appr_approvals); $i++) {
						print_r('<th style="border: 1px solid black; width: 10%;">'.$appr_approvals[$i]->header.'<br>'.$appr_approvals[$i]->remark.'</th>');
					}?>
				</tr>
				<tr style="height: 15px">
					<?php
					for ($i=0; $i < count($appr_approvals); $i++) {
						if ($appr_approvals[$i]->status == 'Approved') {
								print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$appr_approvals[$i]->status.'<br>'.$appr_approvals[$i]->approved_at.'</th>');
						}
						else{
							print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$appr_approvals[$i]->status.'<br>'.$appr_approvals[$i]->approved_at.'</th>');
						}
					}
					?>
				</tr>
				<tr>
					<?php
					for ($i=0; $i < count($appr_approvals); $i++) {
						print_r('<th style="border: 1px solid black; width: 10%;">'.$appr_approvals[$i]->approver_name.'</th>');
					}
					?>
				</tr>
			</thead>
		</table>
					</td>
				</tr>
			</table>
		<br>
		<br>
		
</div>
</div>
</body>
</html>