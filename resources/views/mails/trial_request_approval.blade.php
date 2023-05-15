<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
	<title>Approval Trial Request</title>
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

		table > tbody > tr > td > p > img {
			max-width: 500px !important;
		}

		#sakurentsu_table > tbody > tr > th {
			background-color: rgb(126,86,134);
		}
		#tiga_m_table > tbody > tr > th {
			background-color: #605ca8;
		}

		#implement_table > tbody > tr > th {
			background-color: #605ca8;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 22px; font-weight: bold;">
				<?php if ($data["position"] == "Chief Foreman Issue" || $data['position'] == "Manager Issue" || $data['position'] == "DGM Issue" || $data['position'] == "GM Issue" || $data["position"] == "Manager Mechanical") { ?>
					Approval Trial Request Application <br> 試作依頼書 承認
				<?php } else if ($data["position"] == 'Manager Receiving Trial' || $data["position"] == 'Chief Receiving Trial' || $data['position'] == 'User Receiving Trial') { ?>
					Receive Trial Request Application <br> 試作依頼 受理
				<?php } else if ($data["position"] == 'Trial Result' || $data["position"] == 'Reporting Trial' || $data["position"] == 'Determine 3M') { ?>
					Result Trial Request Application
				<?php } else if ($data["position"] == 'Trial Close' || $data["position"] == 'hold' || $data["position"] == 'reject') { ?>
					Trial Request Application <br> 試作依頼申請
				<?php } ?>
				<br>
			(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			自動通知です。返事しないでください。 <br><br>
			<h2><i class="fa fa-book"></i> {{ $data["datas"]["form_number"] }}</h2>

			<p style="font-size: 18px">
				<!-- TIdak Harus -->
				<b>
					<?php if ($data["position"] == "Manager Receiving Trial") { ?>
						This Trial Request Application has been issued. <br>
						Please add notes Improvement plan/recommendation. <br><br>
					<?php } else if ($data["position"] == "Chief Receiving Trial") { ?>
						This Trial Request Application has been issued. <br>
					<?php } else if ($data["position"] == "User Receiving Trial") { ?>
						This Trial Request Application has been issued and received by the recipient. <br>
					<?php } else if ($data["position"] == "Trial Result" || $data["position"] == 'Reporting Trial') { ?>
						This Trial Request Application has been finished, please upload QC Report. <br>
					<?php } else if ($data["position"] == "Determine 3M") { ?>
						This Trial Request Application has been finished, please determine 3M Requirement <br>
					<?php } else if ($data["position"] == "Trial Close") { ?>
						This Trial Request Application has been CLOSE <br>
					<?php } else if ($data["position"] == "hold") { ?>
						<span style="color: rgb(74, 68, 252); font-size: 20px"> This Trial Request Application has been HOLDED by {{ explode('/',$data["datas"]["reject"])[1] }}</span> <br>
					<?php } else if ($data["position"] == "reject") { ?>
						<span style="color: rgb(252, 68, 68); font-size: 20px">This Trial Request Application has been REJECTED by {{ explode('/',$data["datas"]["reject"])[1] }} </span><br>
					<?php } ?>
				</b>
			</p>
			<?php  if ($data["position"] == 'hold' || $data["position"] == 'reject') { ?>
				<table style="border-color: black; width: 80%" id="sakurentsu_table">
					<tr><th colspan="2" style="background-color: rgb(120, 168, 245);">Comment コメント</th></tr>
					<tr>
						<th style="text-align: left; background-color: rgb(120, 168, 245);" width="35%">Comment コメント</th>
						<td>{{ $data["datas"]["reject_reason"] }}</td>
					</tr>
				</table>
			<?php } ?>
			<!-- Jika Trial Request dari Sakurentsu -->
			<?php  if ($data["datas"]['sakurentsu_number']) { ?>

				<table style="border-color: black; width: 80%" id="sakurentsu_table">
					<tr><th colspan="2" style="background-color: rgb(179, 117, 191);">Sakurentsu 作連通</th></tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);" width="35%">Sakurentsu Number 作連通番号</th>
						<td>{{ $data["datas"]["sakurentsu_number"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">Sakurentsu Title 作連通の表題</th>
						<td>{{ $data["datas"]["title_jp"] }}  {{ $data["datas"]["title"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">Applicant 申請者</th>
						<td>{{ $data["datas"]['applicant'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">Target Date 締切</th>
						<td>{{ $data["datas"]['target_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">Upload Date アップロード日付</th>
						<td>{{ $data["datas"]['upload_date'] }}</td>
					</tr>
				</table>
			<?php }	 ?>
			<br>
			<table style="border-color: black; width: 80%;" id="trial_table">
				<tr><th colspan="2" style="background-color: #dc95fc;">Trial Request Form 試作依頼書</th></tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;" width="35%">Subject 件名</th>
					<td>{{ $data["datas"]['subject'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Target Department 対象部門</th>
					<td>{{ $data["datas"]['department'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Trial Date 試作日付</th>
					<td>{{ $data["datas"]['trial_date'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Trial Purpose 試作目的</th>
					<td> <?php print_r($data["datas"]['trial_purpose']); ?></td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Trial Location 試作場所</th>
					<td>{{ $data["datas"]['trial_location'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Created Date 作成日付け</th>
					<td>{{ $data["datas"]['submit_date'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #dc95fc;">Created By 作成</th>
					<td>{{ $data["datas"]['requester_name'] }}</td>
				</tr>
				<?php if ($data["datas"]['three_m_status']): ?>
					<tr>
						<th style="text-align: left; background-color: #dc95fc;">3M Status 3Mの進捗</th>
						<td>{{ $data["datas"]['three_m_status'] }}</td>
					</tr>
				<?php endif ?>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>


			<?php if ($data["position"] == "Chief Foreman Issue") { $id = $data["datas"]['form_number']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/chief_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/chief_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/chief_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
			<br>			

		<?php }  else if ($data["position"] == "Manager Issue") { $id = $data["datas"]['form_number']; ?>
		<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/manager_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/manager_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/manager_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
		<br>

	<?php }  else if ($data["position"] == "Manager Mechanical") { $id = $data["datas"]['form_number']; ?>
	<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/manager_mechanical') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/manager_mechanical') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/manager_mechanical') }}">&nbsp; Reject (却下) &nbsp;</a>
	<br>
<?php }  else if ($data["position"] == "DGM Issue") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/dgm_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/dgm_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/dgm_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "GM Issue") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/gm_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/gm_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/gm_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "DGM 2 Issue") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/dgm_2_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/dgm_2_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/dgm_2_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "GM 2 Issue") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/sakurentsu/trial_request/'.$id.'/gm_2_issue') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/gm_2_issue') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/gm_2_issue') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "Manager Receiving Trial") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('receive/sakurentsu/trial_request/'.$id.'/manager') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/manager') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/manager') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "Chief Receiving Trial") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('receive/sakurentsu/trial_request/'.$id.'/chief') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/chief') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/chief') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "Trial Result") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('result/sakurentsu/trial_request/'.$id) }}">&nbsp;&nbsp;&nbsp; Write note Report () &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<br>
<?php }  else if ($data["position"] == "approval final pic receiver") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/pic_receiver') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/pic_receiver') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/pic_receiver') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final chief receiver") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/chief_receiver') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/chief_receiver') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/chief_receiver') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final manager receiver") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/manager_receiver') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/manager_receiver') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/manager_receiver') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final pic request") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/pic_request') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/pic_request') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/pic_request') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final chief request") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/chief_request') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/chief_request') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/chief_request') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final manager request") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/manager_request') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/manager_request') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/manager_request') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final dgm") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/dgm') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final gm") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/gm') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/gm') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/gm') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "approval final gm2") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('final/sakurentsu/trial_request/'.$id.'/gm2') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/hold/'.$id.'/gm2') }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/sakurentsu/trial_request/reject/'.$id.'/gm2') }}">&nbsp; Reject (却下) &nbsp;</a>
<br>

<?php }  else if ($data["position"] == "User Receiving Trial" || $data["position"] == "Reporting Trial" || $data["position"] == "Determine 3M" || $data["position"] == "hold" || $data["position"] == "reject") { $id = $data["datas"]['form_number']; ?>
<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/trial_request') }}">&nbsp;&nbsp;&nbsp; Trial Request List &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<br>

<?php } ?>

<br>
<br>
<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('index/sakurentsu/monitoring/3m') }}">&nbsp;&nbsp;&nbsp; Sakurentsu, 3M, Trial Request Monitoring &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 作連通、３M、試作依頼監視 &nbsp;&nbsp;&nbsp;</a>
</center>
</div>
</body>
</html>