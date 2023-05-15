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
			text-align: left;		
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			
			<p style="font-size: 20px; font-weight: bold;">Approval Missing Asset <br> 資産紛失の承認</p>
			This is an automatic notification. Please do not reply to this address. <br>
			自動通知です。返事しないでください。 <br>	
			<?php if ($data['datas']['status'] == 'reject'){ ?>
				<h1 style="color: red">Your Missing Asset Report has been REJECTED</h1>
			<?php } else if ($data['datas']['status'] == 'hold'){ ?>
				<h1 style="color: blue">Your Missing Asset Report has been HOLDED</h1>
			<?php } else if ($data['datas']['status'] == 'presdir'){ ?>
				<h1 style="color: blue">Your Missing Asset Report has been Fully Approved</h1>
			<?php } else if ($data['datas']['status'] == 'approve_doc'){ ?>
				<h1 style="color: blue">Your Missing Asset Report has been Fully Approved</h1>
			<?php } ?>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold"><center>Asset Details 資産の詳細</center></td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Number 固定資産番号</th>
					<td style="width: 50%; text-align: right;">{{ $data['datas']['fixed_asset_id'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Name 固定資産名</th>
					<td style="width: 50%">{{ $data['datas']['fixed_asset_name'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Clasification 分類</th>
					<td style="width: 50%">{{ $data['datas']['clasification'] }}</td>
				</tr>


				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Section Control 管理部署</th>
					<td style="width: 50%">{{ $data['datas']['section_control'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Disposal Reason 処分理由</th>
					<td style="width: 50%">{{ $data['datas']['reason'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Fixed Asset Picture 固定資産写真</th>
					<td style="width: 50%"><img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('files/fixed_asset/missing_picture/'.$data['datas']['new_picture'])))}}" style="max-width: 200px" alt=""></td>
				</tr>

				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold;"><center>Improvement Statement 改善表明</center></td>
				</tr>

				<tr>
					<td colspan="2" style="border: 1px solid black; background-color: #f7da88;"><center>We apologize for  loss(missing) of mentioned fixed asset, to avoid the condition improvement plan is undertaken <br> 固定資産の紛失に申し訳ございません。改善のため防止対策を講じました。</center></td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Reason for Missing</th>
					<td style="width: 50%;">{{ $data['datas']['missing_reason'] }}</td>
				</tr>

				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Improvement Plan 改善計画</th>
					<td style="width: 50%;">{{ $data['datas']['improvement_plan'] }}</td>
				</tr>

				<?php if($data['datas']['status'] != 'created' && $data['datas']['status'] != 'pic') { ?>
					<tr>
						<td colspan="2" style="border: 1px solid black; background-color: #f7da88; font-weight: bold;"><center>Asset Valuation 資産評価</center></td>
					</tr>
					<tr>
						<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">SAP ID</td>
							<td style="width: 50%; text-align: right;">{{ $data['datas']['fixed_asset_id'] }}</th>
							</tr>
							<tr>
								<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Acquisition Date 入手日</th>
								<td style="width: 50%;">{{ $data['datas']['acquisition_date'] }}</td>
							</tr>
							<tr>
								<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">Acquisition Cost 入手金額</th>
								<td style="width: 50%; text-align: right;">$ {{ $data['datas']['acquisition_cost'] }}</td>
							</tr>
							<tr>
								<th style="width: 50%; border:1px solid black; background-color: #c2ff7d; font-weight: bold">NET Book Value 純簿価</th>
								<td style="width: 50%; text-align: right;">$ {{ $data['datas']['book_value'] }}</td>
							</tr>
						<?php } ?>
					</table>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click below to</i> &#8650;</span><br>
					<br>
					<?php if ($data['datas']['status'] == 'created') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/pic") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Reject/pic") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'pic') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("index/fixed_asset/missing") }}">&nbsp;&nbsp;&nbsp; Approve & Fill Acounting Section &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/fa_control") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Reject/fa_control") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'fa_control') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Reject/manager") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'manager') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/gm") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/gm") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Reject/gm") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'gm') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/acc_manager") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/acc_manager") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/acc_manager") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'acc_manager') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/director_fin") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/director_fin") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/director_fin") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'director_fin') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/presdir") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Hold/presdir") }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/presdir") }}">&nbsp; Reject (却下) &nbsp;</a>
						<br>
					<?php } else if($data['datas']['status'] == 'presdir') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/fa_control_upload") }}">&nbsp;&nbsp;&nbsp; Upload Document &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<br>
					<?php } else if($data['datas']['status'] == 'upload_doc') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("approval/fixed_asset/missing/".$data['datas']['form_number']."/Approved/fa_manager_doc") }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<br>
					<?php } ?>


					@if($data['datas']['status'] == 'reject' || $data['datas']['status'] == 'hold')
					<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/fixed_asset/missing") }}">&nbsp;&nbsp;&nbsp; Fixed Asset Missing List &nbsp;&nbsp;&nbsp;</a>
					@endif	
				</center>
			</div>
		</body>
		</html>