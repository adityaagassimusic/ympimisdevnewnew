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
			@if($data[0]->position == "interpreter" || $data[0]->position == "interpreter2" || $data[0]->position == "PC1" || $data[0]->position == "PC2")
			@foreach($data as $datas)
			<?php $id = $datas->id ?>
			<?php $sakurentsu_number = $datas->sakurentsu_number ?>
			<?php $applicant = $datas->applicant ?>
			<?php $title_jp = $datas->title_jp ?>
			<?php $title = $datas->title ?>
			<?php $target_date = $datas->target_date ?>
			<?php $translator = $datas->translator ?>
			<?php $category = $datas->category ?>
			<?php $position = $datas->position ?>
			<?php $status = $datas->status ?>
			@endforeach
			@endif

			@if($data[0]->position == "interpreter" || $data[0]->position == "interpreter2")

			<!-- <h3>Dear Interpreter</h3> -->

			<p style="font-size: 20px;">A New Sakurentsu Has Been Uploaded<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 30%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$category}}</td>
					</tr>
					@if($category == "Trial")
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Send Trial Results 試作結果を送る</td>
						<td style="border:1px solid black;">{{$datas->send_status}}</td>
					</tr>
					@endif
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Date Implementation Target 運用納期</td>
						<td style="border:1px solid black;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<br>
			@if($data[0]->position == "interpreter")


			<br><br>

			@elseif($data[0]->position == "interpreter2")

			<b>Please Translate this Sakurentsu</b><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/sakurentsu/upload_sakurentsu_translate/".$id) }}">&nbsp;&nbsp;&nbsp; Translate Sakurentsu  作連通を翻訳する &nbsp;&nbsp;&nbsp;</a>

			<br><br>

			@endif

			@elseif($data[0]->position == "PC1")

			<h3>Dear PC Team</h3>

			<p style="font-size: 20px;">A New Sakurentsu Has Been Created & Translated</p>
			<br><p style="font-size: 18px">Please Check This Sakurentsu</p>
			This is an automatic notification. Please do not reply to this address. <br>

			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
						<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$data[0]->title}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$category}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
						<td style="border:1px solid black;">{{$translator}}</td>
					</tr>
					@if($category == "Trial")
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Send Trial Results 試作結果を送る</td>
						<td style="border:1px solid black;">{{$datas->send_status}}</td>
					</tr>
					@endif
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Date Implementation Target 運用納期</td>
						<td style="border:1px solid black;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/sakurentsu/detail/".$id) }}">&nbsp;&nbsp;&nbsp; Accept & Select PIC  承認して担当者を指定する &nbsp;&nbsp;&nbsp;</a>

			@if($category == "Trial")
			<a style="background-color: orange; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/extra_order") }}">&nbsp;&nbsp;&nbsp; Create Extra Order &nbsp;&nbsp;&nbsp;</a>
			@endif

			<br><br>

			@elseif($data[0]->position == "PC2")

			<h3>Dear PC Team</h3>

			<p style="font-size: 20px;">A New Sakurentsu Trial Request Has Been Determined<br>
				Please Schedule a Meeting<br>
			(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
						<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$data[0]->title}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$category}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
						<td style="border:1px solid black;">{{$translator}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Date Implementation Target 運用納期</td>
						<td style="border:1px solid black;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/sakurentsu/monitoring/3m") }}">&nbsp;&nbsp;&nbsp; Upload Notulen & Select PIC   議事録をアップロードし、担当者を指定する &nbsp;&nbsp;&nbsp;</a>

			<br><br>

			@elseif($data[0]->position == "PIC" && $data[0]->category == '3M')

			<h3>Dear {{$data[0]->pic}}</h3>

			<p style="font-size: 20px;">A new Sakurentsu 3M has been Made for {{$data[0]->pic}} <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$data[0]->sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
						<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$data[0]->title}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$data[0]->applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Upload Date アップロード日付</td>
						<td style="border:1px solid black;">{{$data[0]->upload_date}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Target Date 締切</td>
						<td style="border:1px solid black;">{{$data[0]->target_date}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
						<td style="border:1px solid black;">{{$data[0]->translator}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$data[0]->category}}</td></td>
					</tr>
				</tbody>
			</table>

			<br>

			<b>Please Check This Sakurentsu and Create 3M Form</b><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/sakurentsu/3m/".$data[0]->sakurentsu_number) }}"">&nbsp;&nbsp;&nbsp; Issue 3M  3Mを発行する &nbsp;&nbsp;&nbsp;</a>

			<br><br>

			<!-- IF TRIAL -->
			@elseif($data[0]->position == "PIC" && $data[0]->category == 'Trial')

			<h3>Dear {{$data[0]->pic}} Manager</h3>

			<p style="font-size: 20px;">A new Trial Request Sakurentsu has been Requested to {{$data[0]->pic}} <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$data[0]->sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
						<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$data[0]->title}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$data[0]->applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Upload Date アップロード日付</td>
						<td style="border:1px solid black;">{{$data[0]->upload_date}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Target Date 締切</td>
						<td style="border:1px solid black;">{{$data[0]->target_date}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
						<td style="border:1px solid black;">{{$data[0]->translator}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$data[0]->category}}</td></td>
					</tr>
				</tbody>
			</table>

			<br>

			<b>Please Check This Sakurentsu and Choose PIC</b><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("index/sakurentsu/assign/") }}/{{$data[0]->sakurentsu_number}}/{{$data[0]->category}}">&nbsp;&nbsp;&nbsp; Choose PIC &nbsp;&nbsp;&nbsp;</a>

			<br><br>

			@elseif($data[0]->position == "PIC2" && $data[0]->category == 'Trial')

			<h3>Dear {{$data[0]->pic}}</h3>

			<p style="font-size: 20px;">A new Trial Request Sakurentsu has been Accepted <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tbody>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
						<td style="border:1px solid black;">{{$data[0]->sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
						<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
						<td style="border:1px solid black;">{{$data[0]->title}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
						<td style="border:1px solid black;">{{$data[0]->applicant}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Upload Date アップロード日付</td>
						<td style="border:1px solid black;">{{$data[0]->upload_date}}</td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Target Date 締切</td>
						<td style="border:1px solid black;">{{$data[0]->target_date}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
						<td style="border:1px solid black;">{{$data[0]->translator}}</td></td>
					</tr>
					<tr>
						<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
						<td style="border:1px solid black;">{{$data[0]->category}}</td></td>
					</tr>
				</tbody>
			</table>

			<br>

			<b>Please Check This Sakurentsu and Issue Trial Request Form</b><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none; color: white; font-size: 20px;" href="{{ url("index/trial_request/") }}">&nbsp;&nbsp;&nbsp; Issue Trial Request Form &nbsp;&nbsp;&nbsp;</a>

			<br><br>

			<!-- IF INFORMATION -->
			@elseif($data[0]->position == "PIC" && $data[0]->category == 'Information')

			<h3>Dear {{$data[0]->pic}} Manager</h3>

			<p style="font-size: 20px;">A new Sakurentsu - Information has been Informed to Your Department
				<br>
				<table style="border:1px solid black; border-collapse: collapse;" width="70%">
					<tbody>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
							<td style="border:1px solid black;">{{$data[0]->sakurentsu_number}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
							<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
							<td style="border:1px solid black;">{{$data[0]->title}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
							<td style="border:1px solid black;">{{$data[0]->applicant}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Upload Date アップロード日付</td>
							<td style="border:1px solid black;">{{$data[0]->upload_date}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Target Date 締切</td>
							<td style="border:1px solid black;">{{$data[0]->target_date}}</td></td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
							<td style="border:1px solid black;">{{$data[0]->translator}}</td></td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
							<td style="border:1px solid black;">{{$data[0]->category}}</td></td>
						</tr>
					</tbody>
				</table>

				<br><br>

				@elseif($data[0]->position == "PIC" && $data[0]->category == 'Not Related')

			<h3>Dear Oyama san</h3>

			<p style="font-size: 20px; font-weight: bold">Sakurentsu - Information of Not Related Sakurentsu to YMPI
				<br>
				<table style="border:1px solid black; border-collapse: collapse;" width="70%">
					<tbody>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Number 作連通番号</td>
							<td style="border:1px solid black;">{{$data[0]->sakurentsu_number}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Japanese Title 日本語の件名</td>
							<td style="border:1px solid black;">{{$data[0]->title_jp}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Title 件名</td>
							<td style="border:1px solid black;">{{$data[0]->title}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Applicant 申請者</td>
							<td style="border:1px solid black;">{{$data[0]->applicant}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Upload Date アップロード日付</td>
							<td style="border:1px solid black;">{{$data[0]->upload_date}}</td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Target Date 締切</td>
							<td style="border:1px solid black;">{{$data[0]->target_date}}</td></td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Translator 翻訳者</td>
							<td style="border:1px solid black;">{{$data[0]->translator}}</td></td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Sakurentsu Category 作連通のカテゴリ</td>
							<td style="border:1px solid black;">{{$data[0]->category}}</td></td>
						</tr>
						<tr>
							<td style="width: 40%; border:1px solid black; font-weight: bold; background-color: #f59fec;">Determined by</td>
							<td style="border:1px solid black;">Mamluatul Atiyah</td></td>
						</tr>
					</tbody>
				</table>

				<br><br>

				@endif
			</center>
		</div>
	</body>
	</html>