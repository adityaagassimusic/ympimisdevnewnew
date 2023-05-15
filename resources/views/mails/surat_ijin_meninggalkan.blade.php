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
		.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
		.button_reject {
		  background-color: #fa3939; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">SURAT IZIN KELUAR PERUSAHAAN<br>外出申請書</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<?php if ($data['leave_request']->position == 'security'){ ?>
				<p style="font-size: 18px;font-weight: bold;">Permintaan Anda telah disetujui oleh HR / GA.<br>Berikan informasi Request ID : <b style="color: red">{{$data['leave_request']->request_id}}</b> ke Security untuk disetujui melalui MIRAI sebelum meninggalkan pabrik.</p>
			<?php } ?>

			<?php if (ISSET($data['driver']) && $data['driver'] != null): ?>
				<p style="font-size: 18px;font-weight: bold;">Driver Anda adalah : {{$data['driver']}}
				<?php if (ISSET($data['phone_no']) && $data['phone_no'] != null): ?>
					<br>No. HP : {{$data['phone_no']}}</p>
				<?php endif ?>
			<?php endif ?>

			<?php if (ISSET($data['edited'])): ?>
				<p style="font-size: 18px;font-weight: bold;">Permintaan ini telah diubah. Abaikan Email sebelumnya dan setujui pada Email ini.</p>
			<?php endif ?>
			<!-- <span style="font-weight: bold; font-size: 26px;">Request ID<br>{{$data['leave_request']->request_id}}</span> -->
		</center>
	</div>			
	<div>
		<center>
			<div style="width: 60%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<!-- <thead style="background-color: rgb(126,86,134);color: white">
					</thead> -->
					<tbody align="center">
						<tr>
							<td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Details</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Request ID
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;color: red">
								{{$data['leave_request']->request_id}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Kategori Keperluan
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{$data['leave_request']->purpose_category}} - {{$data['leave_request']->purpose}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Detail Keperluan
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{$data['leave_request']->purpose_detail}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Detail Kota
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{$data['leave_request']->detail_city}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Waktu Keluar
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{date('d F Y H:i',strtotime($data['leave_request']->time_departure))}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Waktu Kembali
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{date('d F Y H:i',strtotime($data['leave_request']->time_arrived))}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Kembali / Tidak
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{$data['leave_request']->return_or_not}}
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
								Butuh Driver
							</td>
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
								{{$data['leave_request']->add_driver}}
							</td>
						</tr>
					</tbody>
				</table>
				<br><br>
				<table style="border:1px solid black; border-collapse: collapse;width: 60%">
					<!-- <thead style="background-color: rgb(126,86,134);color: white">
					</thead> -->
					<tbody align="center">
						<tr>
							<td colspan="3" style="border:1px solid black; font-size: 15px; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Detail Karyawan</td>
						</tr>
						<tr>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
								ID
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 3%; height: 15;font-weight: bold;background-color: #d4e157;text-align:left;">
								Name
							</td>
							<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;text-align:left;">
								Dept
							</td>
						</tr>
						@foreach($data['detail_emp'] as $emp)
						<tr>
							<td style="border:1px solid black; font-size: 13px; height: 15;text-align:left;">
								{{$emp->employee_id}}
							</td>
							<td style="border:1px solid black; font-size: 13px; height: 15;text-align:left;">
								{{$emp->name}}
							</td>
							<td style="border:1px solid black; font-size: 13px; height: 15;text-align:left;">
								{{$emp->department_shortname}}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				<?php if ($data['leave_request']->purpose == 'SAKIT'): ?>
					<br><br>
					<table style="border:1px solid black; border-collapse: collapse;width: 60%">
						<tbody align="center">
							<tr>
								<td colspan="2" style="border:1px solid black; font-size: 15px; width: 100%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Detail Hasil Klinik</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 25%; height: 15; background-color: #d4e157;text-align:left;">
									Diagnosa
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 75%; height: 15;text-align:left;">
									{{$data['leave_request']->diagnose}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 25%; height: 15; background-color: #d4e157;text-align:left;">
									Tindakan
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 75%; height: 15;text-align:left;">
									{{$data['leave_request']->action}}
								</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 25%; height: 15; background-color: #d4e157;text-align:left;">
									Anjuran
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 75%; height: 15;text-align:left;">
									{{$data['leave_request']->suggestion}}
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif ?>
				<?php if (ISSET($data['destination']) && $data['destination'] != null): ?>
					<br><br>
					<table style="border:1px solid black; border-collapse: collapse;width: 50%">
						<tbody align="center">
							<tr>
								<td colspan="2" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Detail Tujuan</td>
							</tr>
							<tr>
								<td style="border:1px solid black; font-size: 15px; width: 11%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
									#
								</td>
								<td style="border:1px solid black; font-size: 15px; width: 3%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
									Tujuan
								</td>
							</tr>
							<?php $index = 1; ?>
							@foreach($data['destination'] as $destination)
							@if($destination->category == 'destination')
							<tr>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;text-align:right;">
									{{$index}}
								</td>
								<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15;text-align:left;">
									{{$destination->remark}}
								</td>
							</tr>
							<?php $index++ ?>
							@endif
							@endforeach
						</tbody>
					</table>
				<?php endif ?>
			</div>
			<br><br>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<!-- <thead style="background-color: rgb(126,86,134);color: white">
					</thead> -->
					<tbody align="center">
						<tr>
							<td colspan="{{count($data['approval_progress'])}}" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Proses Persetujuan</td>
						</tr>
						<tr>
						@foreach($data['approval_progress'] as $approval)
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
								<?php if ($approval->remark == 'Applicant'){
									echo 'Diajukan Oleh';
								}else if($approval->remark == 'Manager'){
									if ($approval->status == 'Rejected') {
										echo 'Menolak';
									}else{
										echo 'Menyetujui';
									}
								}else{
									echo 'Mengetahui';
								} ?>
							</td>
						@endforeach
						</tr>
						<tr>
						@foreach($data['approval_progress'] as $approval)
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;padding-top:35px;padding-bottom:35px;text-align:left;">
								<?php if ($approval->status == null){
									echo '';
								}else{
									echo $approval->status.'<br>'.date('d M Y',strtotime($approval->approved_at)).'<br>'.date('H:i:s',strtotime($approval->approved_at));
								} ?>
							</td>
						@endforeach
						</tr>
						<tr>
						@foreach($data['approval_progress'] as $approval)
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
								<?php if ($approval->approver_name != ''){
									if (count(explode(' ', $approval->approver_name)) > 1) {
										echo explode(' ', $approval->approver_name)[0].' '.explode(' ', $approval->approver_name)[1];
									}else{
										echo $approval->approver_name;
									}
								}else{
									echo '';
								} ?>
							</td>
						@endforeach
						</tr>
						<tr>
						@foreach($data['approval_progress'] as $approval)
							<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
								{{$approval->remark}}
							</td>
						@endforeach
						</tr>
					</tbody>
				</table>
				<?php if ($data['leave_request']->position != 'security'): ?>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<br>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/human_resource/leave_request/'.$data['leave_request']->request_id.'/'.$data['remarks']) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if ($data['approval_progress'][1]->status == null): ?>
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('reject/human_resource/leave_request/'.$data['leave_request']->request_id.'/'.$data['remarks']) }}">&nbsp; Reject &nbsp;</a>
					<?php endif ?>
				<?php endif ?>
				<!-- <br>
				<br>
				<p>
					<b>Thanks & Regards,</b>
				</p>
				<p>PT. Yamaha Musical Products Indonesia<br>
					Jl. Rembang Industri I / 36<br>
					Kawasan Industri PIER - Pasuruan<br>
					Phone   : 0343 – 740290<br>
					Fax.    : 0343 - 740291
				</p> -->
			</div>
		</center>
	</div>
</body>
</html>