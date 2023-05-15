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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: red; font-size: 24px;">Reminder Penanganan Temuan Patrol</span>
			<br>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="95%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="border:1px solid black; width: 0.1%; font-size: 11px; padding: 0px 0px 0px 0px;text-align: center;">No</th>
						<th style="border:1px solid black; width: 2%; font-size: 11px; padding: 0px 0px 0px 0px;">Name</th>
						<?php
						for ($i=0; $i < count($data['category']); $i++) {
							print_r('<th style="border:1px solid black; width: 1%; font-size:11px; padding: 0px 0px 0px 0px;">'.$data['category'][$i]->kategori.'</th>');
						}
						?>

						<th style="border:1px solid black; width: 0.1%; font-size: 11px; padding: 0px 0px 0px 0px;background-color: RGB(252, 248, 227);">Total</th>
					</tr>
			</thead>
			<tbody>
				<?php
				$auditee = [];
				$no = 1;

				for ($i=0; $i < count($data['audit_data']); $i++) {
					if(!in_array($data['audit_data'][$i]->auditee_name, $auditee)){
						array_push($auditee, $data['audit_data'][$i]->auditee_name);
					}
				}


				$jumlah = 0;

				for ($i=0; $i < count($auditee); $i++) {
					print_r('<tr>');

					print_r('
						<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;">&nbsp;'.$no++.'</td>
					');	

					print_r('
						<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;">&nbsp;'.$auditee[$i].'</td>
					');	

					for ($j=0; $j < count($data['category']); $j++) { 
						$inserted = false;

						for ($k=0; $k < count($data['audit_data']); $k++) {
							if($data['category'][$j]->kategori == $data['audit_data'][$k]->kategori && $data['audit_data'][$k]->auditee_name == $auditee[$i] ){
								
								print_r('<td style="border: 1px solid black; text-align: center; background-color: RGB(255,204,255); padding: 0px 0px 0px 0px; font-size:12px;">'.$data['audit_data'][$k]->jumlah.'</td>');
								$inserted = true;
								$jumlah += $data['audit_data'][$k]->jumlah;
							}
						}
						if(!$inserted){
							print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px;"></td>');
						}
					}
					print_r('
						<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;text-align:center;background-color:RGB(252, 248, 227)">&nbsp;'.$jumlah.'</td>
					');

					print_r('</tr>');
					$jumlah = 0;
				}
			?>
			<tr>
				<th style="border:1px solid black; width: 0.1%; font-size: 11px; padding: 0px 0px 0px 0px;text-align: center;background-color: RGB(252, 248, 227);" colspan="2">Total</th>
				<?php
				$auditee = [];
				$no = 1;

				for ($i=0; $i < count($data['audit_data']); $i++) {
					if(!in_array($data['audit_data'][$i]->auditee_name, $auditee)){
						array_push($auditee, $data['audit_data'][$i]->auditee_name);
					}
				}

				$jumlah_all = 0;
				$jumlah_fix = 0;
					for ($j=0; $j < count($data['category']); $j++) { 
						$inserted = false;

						for ($k=0; $k < count($data['audit_data']); $k++) {
							if($data['category'][$j]->kategori == $data['audit_data'][$k]->kategori ){
								$inserted = true;
								$jumlah_all += $data['audit_data'][$k]->jumlah;
								$jumlah_fix += $data['audit_data'][$k]->jumlah;
							}
						}
						if(!$inserted){
							$jumlah_all += 0;
						}
						
						print_r('
							<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;text-align:center;background-color:RGB(252, 248, 227)">&nbsp;'.$jumlah_all.'</td>
						');

						$jumlah_all = 0;
					}

					print_r('
					<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;text-align:center;background-color:RGB(252, 248, 227)">'.$jumlah_fix.'</td>
				');
					
				?>
			</tr>

		</tbody>
	</table>
	<br>
	<br>
	<a href="http://10.109.52.4/mirai/public/index/patrol">&#10148; Click this link if you want to check monitoring.</a>
</center>
</div>
</body>
</html>