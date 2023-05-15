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
			<span style="font-weight: bold; color: purple; font-size: 24px;">QA KENSA CERTIFICATE APPROVAL<br>品質保証検査認定承認</span><br>
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
		</center>
	</div>			
	<div>
		<center>
			<?php $certificate_id = []; ?>
			<div style="width: 80%">
				<table style="border:1px solid black; border-collapse: collapse;">
					<thead style="background-color: rgb(126,86,134);color: white">
						<tr>
							<th style="font-size: 15px;width: 3%;border:1px solid black;">Certificate No.<br><small>認定番号</small></th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Certificate Name<br><small>認定名</small></th>
							<th style="font-size: 15px;width: 5%;border:1px solid black;">Employee<br><small>従業員</small></th>
							<th style="font-size: 15px;width: 5%;border:1px solid black;">Subject<br><small>件名</small></th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Result<br><small>結果</small></th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Decision<br><small>合格</small></th>
							<th style="font-size: 15px;width: 2%;border:1px solid black;">Preview<br><small>プレビュー</small></th>
						</tr>
					</thead>
					<tbody align="center">
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<?php array_push($certificate_id, $data[$i]['datas']->certificate_id) ?>
							<?php $jumlah_subject = 0; ?>
							<?php for ($j=0; $j < count($data); $j++) { 
								if ($data[$j]['datas_nilai'][0]->certificate_id == $data[$i]['datas']->certificate_id) {
									$jumlah_subject = count($data[$j]['datas_nilai']);
								}
							} ?>
						<tr>
							<td rowspan="<?php echo $jumlah_subject ?>" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->certificate_code}}</td>
							<td rowspan="<?php echo $jumlah_subject ?>" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->certificate_name}}</td>
							<td rowspan="<?php echo $jumlah_subject ?>" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$i]['datas']->employee_id}} - {{$data[$i]['datas']->name}}</td>
							<?php for ($k=0; $k < count($data); $k++) { 
								if ($data[$k]['datas_nilai'][0]->certificate_id == $data[$i]['datas']->certificate_id) { ?>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$k]['datas_nilai'][0]->subject}}</td>
								<?php }
							} ?>
							<?php for ($k=0; $k < count($data); $k++) { 
								if ($data[$k]['datas_nilai'][0]->certificate_id == $data[$i]['datas']->certificate_id) { ?>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data[$k]['datas_nilai'][0]->presentase_result}} %</td>
								<?php }
							} ?>
							<?php for ($k=0; $k < count($data); $k++) { 
								if ($data[$k]['datas_nilai'][0]->certificate_id == $data[$i]['datas']->certificate_id) { ?>
									<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
										<?php if ($data[$k]['datas_nilai'][0]->result_grade == 'LULUS') { ?>
											<span style="color: green">{{$data[$k]['datas_nilai'][0]->result_grade}} 合格</span>
										<?php }else{ ?>
											<span style="color: red">{{$data[$k]['datas_nilai'][0]->result_grade}} 不合格</span>
										<?php } ?>
									</td>
								<?php }
							} ?>
							<td rowspan="<?php echo $jumlah_subject ?>" style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
								<a style="text-decoration: none;color: blue;" href="{{ url('print/qa/certificate/'.$data[$i]['datas']->certificate_id) }}">Preview<br>プレビュー</a>
							</td>
						</tr>
							<?php for ($l=0; $l < count($data); $l++) { 
								if ($data[$l]['datas_nilai'][0]->certificate_id == $data[$i]['datas']->certificate_id) { ?>
									<?php for ($m=1; $m < count($data[$l]['datas_nilai']); $m++) { ?>
										<tr>
										<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">{{$data[$l]['datas_nilai'][$m]->subject}}</td>
										<td style="border:1px solid black; font-size: 15px; height: 20;text-align: right;">{{$data[$l]['datas_nilai'][$m]->presentase_result}} %</td>
										<td style="border:1px solid black; font-size: 15px; height: 20;text-align: left;">
											<?php if ($data[$l]['datas_nilai'][$m]->result_grade == 'LULUS') { ?>
												<span style="color: green">{{$data[$l]['datas_nilai'][$m]->result_grade}} 合格</span>
											<?php }else{ ?>
												<span style="color: red">{{$data[$l]['datas_nilai'][$m]->result_grade}} 不合格</span>
											<?php } ?>
										</td>
										</tr>
									<?php } ?>
								<?php }
							} ?>				
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br><br>
			<div style="width: 80%">
				<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
					(下をクリックしてください)<br>
					<?php if ($data[0]['next_remark'] == 'Staff QA') { ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/qa/certificate/'.$data[0]['next_remark']) }}">&nbsp;&nbsp;&nbsp; Approve <small>(承認)</small> &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/qa/certificate/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Reject <small>(却下)</small> &nbsp;&nbsp;&nbsp;</a>
					<?php }else{ ?>
						<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval_all/qa/certificate/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Approve <small>(承認)</small> &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;margin-left: 50px" href="{{ url('reject/qa/certificate/'.$data[0]['next_remark'].'/'.join(',',$certificate_id)) }}">&nbsp;&nbsp;&nbsp; Reject <small>(却下)</small> &nbsp;&nbsp;&nbsp;</a>
					<?php } ?>
					<br>
					<br>
					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a style="width: 50px;text-decoration: underline;" href="{{ url('index/qa/certificate/code') }}">Monitoring 監視</a>
				<br>
			</div>
		</center>
	</div>
</body>
</html>