<!DOCTYPE html>
<html>
<head>
	<title>Approval Disposal Fixed Asset</title>
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
			text-align: left;	
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<?php if ( $data['datas']['status'] == 'acc_label') { ?>
				<p style="font-size: 20px; font-weight: bold;">Fixed Asset Disposal <br> 固定資産の処分</p>
				<p style="font-size: 20px; font-weight: bold;">A New Asset Disposal Form Has Been Fully Approved, Please Print The Label</p>
			<?php } else { ?>
				<p style="font-size: 20px; font-weight: bold;">Approval Disposal Asset <br> 資産処分の承認</p>
			<?php } ?>

			<?php if ($data['datas']['status'] == 'new_pic') {
				echo '<p style="font-size: 20px; font-weight: bold;">Please upload Payment Receipt</p>';
			} else if ($data['datas']['status'] == 'upload_payment') {
				echo '<p style="font-size: 20px; font-weight: bold;">Payment Receipt</p>';
			} 
			?>

			This is an automatic notification. Please do not reply to this address. <br>
			自動通知です。返事しないでください。<br><br>
			<?php if ($data['datas']['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Asset Disposal has been REJECTED</h1>
				<table style="border:1px solid black; border-collapse: collapse;" width="70%">
					<tr>
						<td style="width: 30%">{{ explode('/',$data['datas']['reject_status'])[1] }}</td>
						<td style="width: 70%">{{ $data['datas']['comment'] }}</td>
					</tr>
				</table>
			<?php } else if ($data['datas']['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Asset Disposal has been HOLDED</h1>
				<table style="border:1px solid black; border-collapse: collapse;" width="70%">
					<tr>
						<td style="width: 30%">{{ explode('/',$data['datas']['reject_status'])[1] }}</td>
						<td style="width: 70%">{{ $data['datas']['comment'] }}</td>
					</tr>
				</table>
			<?php } ?>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold; text-align: center"><center>Asset Details 資産の詳細</center></td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; text">Fixed Asset Number 固定資産番号</th>
					<td style="width: 50%; text-align: right;">{{ $data['datas']['fixed_asset_id'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name 固定資産名</th>
					<td style="width: 50%">{{ $data['datas']['fixed_asset_name'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Section Control 管理部署</th>
					<td style="width: 50%">{{ $data['datas']['section_control'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Clasification 分類</th>
					<td style="width: 50%">{{ $data['datas']['clasification_id'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Disposal Mode 処分モード</th>
					<td style="width: 50%">{{ $data['datas']['mode'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">PIC In Charge 担当者</th>
					<td style="width: 50%">{{ $data['datas']['pic_incharge'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Disposal Reason 処分理由</th>
					<td style="width: 50%">{{ $data['datas']['reason'] }} <br> {{ $data['datas']['reason_jp'] }}</td>
				</tr>

				<?php if ($data['datas']['mode'] == 'SCRAP') { ?>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Disposal Request Date 処分申請日</th>
						<td style="width: 50%">{{ $data['datas']['disposal_request_date'] }}</td>
					</tr>
				<?php } ?>

				<?php if ($data['datas']['status'] != 'created' || $data['datas']['status'] != 'created') { ?>

					<tr>
						<th colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold; text-align: center;">Asset Valuation 資産評価</th>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Registration Amount 登録金額</th>
						<td style="width: 50%; text-align: right;">$ {{ $data['datas']['registration_amount'] }}</td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Registration Date 登録日</th>
						<td style="width: 50%;">{{ $data['datas']['registration_date'] }}</td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Vendor ベンダー</th>
						<td style="width: 50%;">{{ $data['datas']['vendor'] }}</td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">NET Book Value 純簿価</th>
						<td style="width: 50%; text-align: right;">$ {{ $data['datas']['book_value'] }}</td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Invoice Number インボイス番号</th>
						<td style="width: 50%;">{{ $data['datas']['invoice_number'] }}</td>
					</tr>
				<?php } ?>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
			<br>
			<?php if ($data['datas']['status'] == 'created') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/pic") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/pic") }}">&nbsp; Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'pic') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/disposal") }}">&nbsp;&nbsp;&nbsp; Approve & Fill Acounting Section &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/fa_control") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/fa_control") }}">&nbsp; Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'fa_control') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/manager") }}">&nbsp; Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'manager') { ?>
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/manager_disposal") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/manager_disposal") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/manager_disposal") }}">&nbsp; Reject (却下) &nbsp;</a>
				<br>
			<?php } else if($data['datas']['status'] == 'manager_disposal') { 
				if ($data['datas']['dgm_app']) { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/dgm") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/dgm") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/dgm") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else {	?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/gm") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/gm") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Reject/gm") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } } else if($data['datas']['status'] == 'dgm') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/gm") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/gm") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/gm") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else if($data['datas']['status'] == 'gm') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/acc_manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/acc_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/acc_manager") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else if($data['datas']['status'] == 'acc_manager') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/director_fin") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/director_fin") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/director_fin") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else if($data['datas']['status'] == 'director_fin') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/presdir") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/presdir") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/presdir") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else if($data['datas']['status'] == 'presdir') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/new_pic2") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Hold/new_pic2") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

					<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/new_pic2") }}">&nbsp; Reject (却下) &nbsp;</a>
					<br>
				<?php } else if($data['datas']['status'] == 'new_pic') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/payment") }}">&nbsp;&nbsp;&nbsp; Upload Payment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<br>
				<?php } else if($data['datas']['status'] == 'upload_payment') { ?>
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/disposal/".$data['datas']['id']."/Approved/payment_app") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<br>
				<?php } ?>

				@if($data['datas']['status'] == 'reject' || $data['datas']['status'] == 'hold')
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/disposal") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Disposal List &nbsp;&nbsp;&nbsp;</a>
				@endif	

				<?php 
				if (isset($data['versi'])) {
					if ($data['versi'] == 'web') {
						print_r('<br><br><a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black;" href="'.url("files/fixed_asset/report_disposal/Disposal_".$data['datas']['form_number'].".pdf").'" target="_blank">&nbsp;&#9993; Disposal_'.$data['datas']['form_number'].'.pdf&nbsp;</a>&nbsp;');

						if ($data['datas']['mdoe'] == 'SALE') {
							print_r('<a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black;" href="'.url("files/fixed_asset/report_disposal/".$data['datas']['quotation_file']).'" target="_blank">&nbsp;&#9993; '.$data['datas']['quotation_file'].'&nbsp;</a>&nbsp;');
						}
					}
				}
				?>
			</center>
		</div>
	</body>
	</html>