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
				<?php $ids = $data["datas"]['id']; ?>
				<?php if ($data["position"] == "PRESDIR"  || $data["position"] == "SIGNING" || $data["position"] == "SIGNING DGM" || $data["position"] == "SIGNING GM" || $data["position"] == "DEPT APPROVAL" || $data["position"] == "PIC APPROVAL" || $data["position"] == "SIGNING DGM 2") { ?>
					3M変更申請の承認 Approval 3M Application
				<?php } else if ($data["position"] == "STD" || $data["position"] == "ALL 1" || $data["position"] == "ALL 2"  || $data["position"] == "IMPLEMENT" || $data["position"] == "TRANSLATE" || $data["position"] == "INTERPRETER" || $data["position"] == "INTERPRETER2" || $data["position"] == "DOCUMENT" || $data['position'] == "IMPLEMENT DEPT" || $data['position'] == "IMPLEMENT DGM" || $data['position'] == "STD") { ?>
					3M申請書 3M Application
				<?php } ?>
				<br>
				<?php if($data["position"] == "IMPLEMENT" || $data['position'] == "IMPLEMENT DEPT" || $data['position'] == "IMPLEMENT DGM" || $data['position'] == "IMPLEMENT GM" || $data["position"] == "IMPLEMENT STD") { ?>
					3M変更実行承認 Approval 3M Implementation
				<?php } else if ($data["position"] == "IMPLEMENT INFORMATION") { ?>
					3M実行報告 3M Implementation
				<?php } ?>
			</p>
			<p>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			自動通知です。返事しないでください。 <br><br>
			<p style="font-size: 18px; font-weight: bold">
				<?php if ($data["position"] == "ALL 1" || $data["position"] == "ALL 2") { ?>
					This 3M Application has fully approved, Now it can be implemented. <br>
					<?php if ($data["position"] == "ALL 2") { ?>
						Don't forget to make the implementation Report. <br><br>
					<?php } ?>
				<?php } else if ($data["position"] == "IMPLEMENT" || $data['position'] == "IMPLEMENT DEPT" || $data['position'] == "IMPLEMENT DGM" || $data['position'] == "IMPLEMENT GM" || $data["position"] == "IMPLEMENT STD") { ?>
					This 3M Application has been implemented and checked by PIC. <br>
					Please verify this Implementation Report. <br><br>
				<?php } else if ($data["position"] == "INTERPRETER") { ?>
					This 3M Application has been created. <br>
					Please Choose PIC to Translate <br><br>
				<?php } else if ($data["position"] == "INTERPRETER2") { ?>
					This 3M Application has been created. <br>
					Please Translate this 3M Application. <br><br>
				<?php } else if ($data["position"] == "TRANSLATE") { ?>
					This 3M Application has been translated. <br>
					<?php if ($data["remark"] == '') { ?>
						Please Check and don't forget to schedule a meeting. <br><br>
					<?php } ?>
				<?php } else if ($data["position"] == "DOCUMENT") { ?>
					This 3M Application document has been uploaded all. <br><br>
				<?php } else if ($data["position"] == "DEPT APPROVAL" || $data["position"] == "PIC APPROVAL" || $data["position"] == "PRESDIR") { ?>
					Please verify this 3M Application. <br><br>
				<?php } else if ($data["position"] == "STD") { ?>
					Please receive this 3M Application. <br><br>
				<?php } else if ($data["position"] == "IMPLEMENT INFORMATION") { ?>
					This 3M Application has been Closed and checked by PIC <br><br>
				<?php } ?>
			</p>

			<!-- Jika 3M dari Sakurentsu -->
			<?php  if ($data["datas"]['sakurentsu_number']) { ?>

				<table style="border-color: black; width: 80%" id="sakurentsu_table">
					<tr><th colspan="2" style="background-color: #f59fec">作連通 Sakurentsu</th></tr>
					<tr>
						<th style="text-align: left; background-color: #f59fec" width="35%">作連通番号 Sakurentsu Number</th>
						<td>{{ $data["datas"]["sakurentsu_number"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #f59fec">作連通の表題 Sakurentsu Title</th>
						<td>{{ $data["datas"]["title_sakurentsu_jp"] }}  {{ $data["datas"]["title_sakurentsu"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #f59fec">申請者 Applicant</th>
						<td>{{ $data["datas"]['applicant'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #f59fec">締切 Target Date</th>
						<td>{{ $data["datas"]['target_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #f59fec">アップロード日付 Upload Date</th>
						<td>{{ $data["datas"]['upload_date'] }}</td>
					</tr>
				</table>
			<?php }	 ?>
			<br>
			<table style="border-color: black; width: 80%;" id="tiga_m_table">
				<tr><th colspan="2" style="background-color: #c79cf7;">3M申請書 3M Application</th></tr>
				<tr>
					<th rowspan="2" style="text-align: left; background-color: #c79cf7;" width="35%">3M変更表題 3M Title</th>
					<td>{{ $data["datas"]['title'] }}</td>
				</tr>
				<tr>
					<td>{{ $data["datas"]['title_jp'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7;">製品名 Product Name</th>
					<td>{{ $data["datas"]['product_name'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7;">工程名 Proccess Name</th>
					<td>{{ $data["datas"]['proccess_name'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7;">班名 Unit Name</th>
					<td>{{ $data["datas"]['unit'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7;">3M変更種類 3M Category</th>
					<td>{{ $data["datas"]['category'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #c79cf7;">作成日付け Created Date</th>
					<td>{{ $data["datas"]['created_at'] }}</td>
				</tr>
				<?php if ($data["position"] == "STD") { ?>
					<tr>
						<th style="text-align: left; background-color: #c79cf7;">Presdir Approve Date</th>
						<td>{{ $data['presdir_app_date'] }}</td>
					</tr>
				<?php } ?>
			</table>
			<br>

			<!-- Jika Sudah implementasi -->
			<?php if ($data["position"] == "IMPLEMENT" || $data["position"] == "IMPLEMENT DEPT" || $data["position"] == "IMPLEMENT DGM" || $data["position"] == "IMPLEMENT GM" || $data["position"] == "IMPLEMENT STD" || $data["position"] == "IMPLEMENT INFORMATION") { ?>
				<table style="border-color: black; width: 80%;" id="implement_table">
					<tr><th colspan="2" style="background-color: #ae7ce6;">3M実行報告 3M Implementation Report</th></tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;" width="35%">3M番号 3M Number</th>
						<td>{{ $data["implement"]['form_number'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">部門 Department</th>
						<td>{{ $data["implement"]['section'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">発議者 Proposer</th>
						<td>{{ $data["implement"]['name'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">3M発行日付 Date Issued 3M</th>
						<td>{{ $data["implement"]['frm_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">3M変更表題 3M Title</th>
						<td>{{ $data["implement"]['title'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">製番 Serial Number</th>
						<td>{{ $data["implement"]['serial_number'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">切り替え日付（予定） Planned Change Date</th>
						<td>{{ $data["implement"]['started_date'] }} <br> {{ $data["implement"]['date_note'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">切り替え日付（実際）Actual Change Date</th>
						<td>{{ $data["implement"]['act_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">Check Date</th>
						<td>{{ $data["implement"]['ck_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #ae7ce6;">Checker</th>
						<td>{{ $data["implement"]['checker'] }}</td>
					</tr>
				</table>
				<br>
			<?php } ?>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>

			<table style="width: 80%; text-align: center; border: 0px" id="ttd_table">
				<tr>
					<?php if ($data["position"] == "PRESDIR") { $id = $data["datas"]['id']; ?>
					<td style="border: 0px">
						<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/presdir') }}">&nbsp;&nbsp;&nbsp; View 3M Detail & Approval (3M変更の詳細＆承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;						
					</td>

				<?php }  else if ($data["position"] == "STD") { $id = $data["datas"]['id']; ?>

				<td style="border: 0px">
					<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/'.$id.'/std') }}">&nbsp;&nbsp;&nbsp; Receive &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/finish') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp;</a>
				</td>
			<?php }  else if ($data["position"] == "PIC APPROVAL") { $id = $data["datas"]['id']; ?>

			<td style="border: 0px">
				<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/pic/'.$id.'/pic') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
			</td>

		<?php }  else if ($data["position"] == "DEPT APPROVAL") { $id = $data["datas"]['id']; ?>

		<td style="border: 0px">
			<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/'.$id.'/dept') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
		</td>

	<?php }  else if ($data["position"] == "ALL 1" || $data["position"] == "ALL 2") { $id = $data["datas"]['id']; ?>

	<td style="border: 0px">
		<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
	</td>

<?php }  else if ($data["position"] == "IMPLEMENT DEPT") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/implementation/sign/'.$id.'/dept') }}">&nbsp;&nbsp;&nbsp; Verify 3M Implementation &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更実行検証 &nbsp;&nbsp;&nbsp;</a>
</td>
<td style="border: 0px">
	<a style="background-color: #fa932d;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "IMPLEMENT") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/implementation/sign/'.$id.'/proposer') }}">&nbsp;&nbsp;&nbsp; Verify 3M Implementation &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更実行検証 &nbsp;&nbsp;&nbsp;</a>
</td>
<td style="border: 0px">
	<a style="background-color: #fa932d;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "IMPLEMENT DGM") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/implementation/sign/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; Verify 3M Implementation &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更実行検証 &nbsp;&nbsp;&nbsp;</a>
</td>
<td style="border: 0px">
	<a style="background-color: #fa932d;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "IMPLEMENT GM") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/implementation/sign/'.$id.'/gm') }}">&nbsp;&nbsp;&nbsp; Approve 3M Implementation &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更承認 &nbsp;&nbsp;&nbsp;</a>
</td>
<td style="border: 0px">
	<a style="background-color: #fa932d;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "IMPLEMENT STD") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/implementation/sign/'.$id.'/std') }}">&nbsp;&nbsp;&nbsp; Receive 3M Implementation &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更受け取り &nbsp;&nbsp;&nbsp;</a>
</td>
<td style="border: 0px">
	<a style="background-color: #fa932d;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "TRANSLATE") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<!-- <a style="background-color: #4ecc4b;color: white;font-size:20px;" href="{{ url('index/sakurentsu/list_3m') }}">&nbsp;&nbsp;&nbsp; view 3M List &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更リストを見る &nbsp;&nbsp;&nbsp;</a> -->

	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/sakurentsu/list_3m') }}">&nbsp;&nbsp;&nbsp; View 3M List (3M変更リストを見る) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>

<?php }  else if ($data["position"] == "INTERPRETER") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/sakurentsu/assign/".$id."/interpreter_tiga_em") }}">&nbsp;&nbsp;&nbsp; Choose PIC to Translate this 3M &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "INTERPRETER2") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/sakurentsu/3m/translate/'.$id) }}">&nbsp;&nbsp;&nbsp; Translate 3M &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "DOCUMENT") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Report (??) &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "SIGNING") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/sign') }}">&nbsp;&nbsp;&nbsp; View 3M Detail & Approval (3M変更の詳細＆承認) &nbsp;&nbsp;&nbsp;</a>
</td>

<?php }  else if ($data["position"] == "SIGNING DGM") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
	<!-- <a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; View 3M Detail & Approval (3M変更の詳細＆承認) &nbsp;&nbsp;&nbsp;</a> -->
</td>

<?php }  else if ($data["position"] == "SIGNING DGM 2") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/'.$id.'/dgm2') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
	<!-- <a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/dgm') }}">&nbsp;&nbsp;&nbsp; View 3M Detail & Approval (3M変更の詳細＆承認) &nbsp;&nbsp;&nbsp;</a> -->
</td>

<?php }  else if ($data["position"] == "SIGNING GM") { $id = $data["datas"]['id']; ?>

<td style="border: 0px">
	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approve/sakurentsu/3m/sign/'.$id.'/gm') }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
	<!-- 		<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/gm') }}">&nbsp;&nbsp;&nbsp; View 3M Detail & Approval (3M変更の詳細＆承認) &nbsp;&nbsp;&nbsp;</a> -->

</td>

<?php } else if ($data["position"] == "IMPLEMENT INFORMATION") { $id = $data["datas"]['id']; ?>
	<td style="border: 0px">
		<a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail (3M変更の詳細) &nbsp;&nbsp;&nbsp;</a>
	</td>
<?php } ?>
</tr>
</table>
<br>
<br>
<a style="background-color: #6b56e3; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('index/sakurentsu/monitoring/3m') }}">&nbsp;&nbsp;&nbsp; Sakurentsu, 3M, Trial Request Monitoring (作連通、３M、試作依頼監視) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</center>
</div>
</body>
</html>