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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; font-size: 26px;">({{ $data['form']->form_id }})<br/>{{ $data['form']->form_name }}</span>
			@if($data['status'] == 'Rejected')
			<br/>
			<span style="font-weight: bold; font-size: 36px; color: red;">REJECTED</span>
			@endif
			@if($data['status'] == 'Fully Approved')
			<br/>
			<span style="font-weight: bold; font-size: 36px; color: green;">FULLY APPROVED</span>
			@endif
			<table>
				<tbody>
					<tr>
						<td style="font-weight: bold;" colspan="3">Submitted By</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['form']->created_by }} - {{ $data['form']->created_by_name }}</td>
					</tr>
				</tbody>
			</table>
		</center>
		<br>
		<div style="width: 90%; margin: auto;">
			<table>
				<tbody>
					<tr>
						<td style="" colspan="3">Hereby noted that<br/><br/></td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Employee ID</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['form']->employee_id }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Employee Name</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['form']->employee_name }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Department</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['form']->department }} ({{ $data['form']->department_shortname }})</td>
					</tr>
					<tr>
						<td style="" colspan="3"><br/><?= $data['form']->form_description ?></td>
					</tr>
				</tbody>
			</table>
			<center>
				Menuju Monitoring Form Permohonan MIS.
				<br/>
				<a href="{{ url('/index/mis/form') }}">MIS Form</a>
				<br/>
				<table style="border: 1px solid black; border-collapse: collapse;" align="right">
					<tbody>
						<tr>
							<?php
							for ($i=0; $i < count($data['form_approvers']); $i++) {
								print_r('<td style="border: 1px solid black; width: 10%; text-align: center; font-weight: bold;">'.$data['form_approvers'][$i]->remark.'<br/>'.$data['form_approvers'][$i]->position.'</td>');
							} ?>
						</tr>
						<tr>
							<?php
							for ($i=0; $i < count($data['form_approvers']); $i++) {
								print_r('<td style="border: 1px solid black; width: 10%; text-align: center; height: 80px;">'.$data['form_approvers'][$i]->status.'<br>'.$data['form_approvers'][$i]->approved_at.'</td>');
							} ?>
						</tr>
						<tr>
							<?php
							for ($i=0; $i < count($data['form_approvers']); $i++) {
								print_r('<td style="border: 1px solid black; width: 10%; text-align: center; font-weight: bold;">'.$data['form_approvers'][$i]->approver_name.'</td>');
							} ?>
						</tr>
					</tbody>
				</table>
			</center>
		</div>
	</div>
</body>
</html>