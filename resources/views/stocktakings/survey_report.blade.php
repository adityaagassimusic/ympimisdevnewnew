@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
	
	#loading {
		display: none;
		margin: 0px;
		padding: 0px;
		position: fixed;
		right: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		background-color: rgb(0,191,255);
		z-index: 30001;
		opacity: 0.8;
	}

	#last_update {
		color: white;
		margin-right: 1.5%;
		padding-top: 0px;
		padding-right: 0px;
		font-size: 1vw;
	}
	
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading">
		<center>
			<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
		</center>
	</div>
	<div class="row">
		<div class="col-xs-2">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: #ccff90;">
					<i class="fa fa-calendar-o"></i>
				</div>
				<input type="text" onchange="fetchChart()" class="form-control monthpicker" name="month" id="month" placeholder="Select Month">  
			</div>
		</div>
		<div class="pull-right" id="last_update"></div>
	</div>

	<div class="row">
		<div class="col-xs-12" id="chart1" style="height: 50vh;"></div>
		<div class="col-xs-6" id="chart2" style="height: 50vh;"></div>
		<div class="col-xs-6" id="chart3" style="height: 50vh;"></div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog" style="width: 85%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 10%; text-align: center; vertical-align: middle;">Employee ID</th>
								<th style="width: 20%; text-align: center; vertical-align: middle;">Name</th>
								<th style="width: 5%; text-align: center; vertical-align: middle;">Department</th>
								<th style="width: 25%; text-align: center; vertical-align: middle;">Section</th>
								<th style="width: 22.5%; text-align: center; vertical-align: middle;">Group</th>
								<th style="width: 17.5%; text-align: center; vertical-align: middle;">Remark</th>
							</tr>
						</thead>
						<tbody id="bodyDetail">
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>



@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchChart();

		$('.select2').select2({
			allowClear : true,
		});

		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

	});

	var employees = [];
	var surveys = [];

	var resume_answer = [];
	var resume_score = [];

	function resumeData() {

		//Resume Employee
		resume_answer = [];
		resume_score = [];

		var temp_answer = [];
		var temp_score = [];
		temp_score['BELUM MENJAWAB'] = {'quantity' : 0};
		temp_score['TIDAK MENGERTI'] = {'quantity' : 0};
		temp_score['KURANG'] = {'quantity' : 0};
		temp_score['CUKUP'] = {'quantity' : 0};
		temp_score['MENGERTI'] = {'quantity' : 0};
		
		for (var i = 0; i < employees.length; i++) {

			//Answer
			var tidak = 0;
			var kurang = 0;
			var cukup = 0;
			var mengerti = 0;

			var remark = 0;
			for (var j = 0; j < surveys.length; j++) {
				if(employees[i].employee_id == surveys[j].employee_id){

					if(surveys[j].remark == 'TIDAK MENGERTI'){
						tidak = 1;
					}else if(surveys[j].remark == 'KURANG'){
						kurang = 1;
					}else if(surveys[j].remark == 'CUKUP'){
						cukup = 1;
					}else if(surveys[j].remark == 'MENGERTI'){
						mengerti = 1;
					}

					remark = surveys[j].remark;
					break;
				}
			}

			var key_answer = employees[i].department_shortname;
			if(!temp_answer[key_answer]) {
				temp_answer[key_answer] = {
					'department_shortname' : employees[i].department_shortname,
					'total' : 1,
					'tidak' : tidak,
					'kurang' : kurang,
					'cukup' : cukup,
					'mengerti' : mengerti,
				};
			} else {
				temp_answer[key_answer].total += 1;
				temp_answer[key_answer].tidak += tidak;
				temp_answer[key_answer].kurang += kurang;
				temp_answer[key_answer].cukup += cukup;
				temp_answer[key_answer].mengerti += mengerti;
			}


			//Score
			var key_score = '';
			if( (tidak + kurang + cukup + mengerti) == 0){
				var key_score = 'BELUM MENJAWAB';
			}else{
				var key_score = remark;
			}

			if(!temp_score[key_score]) {
				temp_score[key_score] = {
					'quantity' : 1,
				};
			} else {
				temp_score[key_score].quantity += 1;
			}

		}

		for (var key_answer in temp_answer) {
			resume_answer.push(temp_answer[key_answer]);
		}


		resume_score = temp_score;

	}

	function fetchChart(){
		var month = $('#month').val();
		var data = {
			month:month
		}

		$('#loading').show();

		$.get('{{ url("fetch/stocktaking/survey_report") }}', data, function(result, status, xhr) {
			if(result.status){

				if(result.surveys.length > 0){
					$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ result.now +'</p>');

					employees = result.employees;
					surveys = result.surveys;

					resumeData();
					var xCategories = [];

					var tidak = [];
					var kurang = [];
					var cukup = [];
					var mengerti = [];
					var unanswer = [];

					for (var i = 0; i < resume_answer.length; i++) {
						xCategories.push(resume_answer[i].department_shortname);
						tidak.push(resume_answer[i].tidak);
						kurang.push(resume_answer[i].kurang);
						cukup.push(resume_answer[i].cukup);
						mengerti.push(resume_answer[i].mengerti);
						unanswer.push((resume_answer[i].total - resume_answer[i].tidak - resume_answer[i].kurang - resume_answer[i].cukup - resume_answer[i].mengerti));
					}

					Highcharts.chart('chart1', {
						chart: {
							type: 'column',
							options3d: {
								enabled: true,
								alpha: 0,
								beta: 0,
								viewDistance: 20,
								depth: 80
							},
							backgroundColor	: null
						},
						title: {
							text: 'Stocktaking Survey',
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'ON '+result.month,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							}
						},
						credits: {
							enabled: false
						},
						legend:{
							enabled: true
						},
						xAxis: {
							categories: xCategories,
						},
						yAxis: {
							title: {
								text: 'Person'
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y}</b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							column: {
								pointPadding: 0.05,
								groupPadding: 0.1,
								borderWidth: 0
							},
							series: {
								dataLabels: {
									enabled: true,
									style:{
										fontSize: '12px;'
									}
								},
								cursor : 'pointer',
								point: {
									events: {
										click: function (event) {
											showDetailChart1(event.point.category, event.point.series.name, result.month);

										}
									}
								},
							},
						},
						series: [
						{
							name: 'Belum Menjawab',
							data: unanswer,
							color: '#e1e5ea'
						},{
							name: 'Tidak Mengerti',
							data: tidak,
							color: '#e75959'
						},{
							name: 'Kurang',
							data: kurang,
							color: '#ffdd66'
						},{
							name: 'Cukup',
							data: cukup,
							color: '#66beff'
						},{
							name: 'Mengerti',
							data: mengerti,
							color: '#4bc16b'
						}
						]
					});

					console.log(resume_score);

					var unanswer = resume_score['BELUM MENJAWAB'].quantity;
					var tidak = resume_score['TIDAK MENGERTI'].quantity;
					var kurang = resume_score['KURANG'].quantity;
					var cukup = resume_score['CUKUP'].quantity;
					var mengerti = resume_score['MENGERTI'].quantity;


					Highcharts.chart('chart2', {
						chart: {
							type: 'pie',
							options3d: {
								enabled: true,
								alpha: 45,
								beta: 0
							},
							backgroundColor	: null
						},
						title: {
							text: 'Answer By Person',
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							}
						},
						accessibility: {
							point: {
								valueSuffix: '%'
							}
						},
						tooltip: {
							pointFormat: 'Total: <b>{point.y} Person</b>'
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								depth: 35,
								dataLabels: {
									enabled: true,
									format: '{point.name} : {point.y}'
								},
								point: {
									events: {
										click: function (event) {
											showDetailChart2(event.point.options.name, result.month);
										}
									}
								},
							},
						},
						series: [{
							type: 'pie',
							name: 'Person',
							data: [
							{
								name: 'Belum Menjawab',
								y: unanswer,
								color: '#ff7272'
							},
							{
								name: 'Sudah Menjawab',
								y: tidak + kurang + cukup + mengerti,
								color: '#64da84'
							},
							]
						}]
					});

					Highcharts.chart('chart3', {
						chart: {
							type: 'pie',
							options3d: {
								enabled: true,
								alpha: 45,
								beta: 0
							},
							backgroundColor	: null
						},
						title: {
							text: 'Answer By category',
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							}
						},
						accessibility: {
							point: {
								valueSuffix: '%'
							}
						},
						tooltip: {
							pointFormat: 'Total: <b>{point.y} Person</b>'
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								depth: 35,
								dataLabels: {
									enabled: true,
									format: '{point.name} : {point.y}'
								},
								point: {
									events: {
										click: function (event) {
											showDetailChart3(event.point.options.name, result.month);
										}
									}
								},
							}
						},
						series: [{
							type: 'pie',
							name: 'Person',
							data: [
							{
								name: 'Belum Menjawab',
								y: unanswer,
								color: '#e1e5ea'
							},
							{
								name: 'Tidak Mengerti',
								y: tidak,
								color: '#ff7272'
							},
							{
								name: 'Kurang',
								y: kurang,
								color: '#fff67f'
							},
							{
								name: 'Cukup',
								y: cukup,
								color: '#7fd7ff'
							},
							{
								name: 'Mengerti',
								y: mengerti,
								color: '#64da84'
							},
							]
						}]
					});
				}else{
					openErrorGritter('Error!', 'Survey data not found');
				}
				$('#loading').hide();

			}else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);

			}
		});		
}

function showDetailChart3(series, month) {

	console.log(series+'_'+month);

	$('#loading').show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#bodyDetail').html("");
	var tableData = "";

	if(series == 'Belum Menjawab'){
		for (var j = 0; j < employees.length; j++) {
			var answer = false;
			for (var k = 0; k < surveys.length; k++) {
				if(surveys[k].employee_id == employees[j].employee_id){
					answer = true;
					break;
				}
			}

			if(answer){
				continue;
			}else{
				tableData += '<tr>';
				tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].employee_id +'</td>';
				tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].name +'</td>';
				tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].department_shortname +'</td>';
				tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].section +'</td>';
				tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].group +'</td>';
				tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">Belum Menjawab</td>';
				tableData += '</tr>';
			}
		}

	}else{
		for (var i = 0; i < surveys.length; i++) {

			var curr_emp = '';
			for (var j = 0; j < employees.length; j++) {
				if(surveys[i].employee_id == employees[j].employee_id){
					curr_emp = employees[j];
					break;
				}
			}

			if(curr_emp != ''){
				if(series.toUpperCase() == surveys[i].remark){
					tableData += '<tr>';
					tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.employee_id +'</td>';
					tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.name +'</td>';
					tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.department_shortname +'</td>';
					tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.section +'</td>';
					tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.group +'</td>';
					tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ surveys[i].remark +'</td>';
					tableData += '</tr>';
				}
			}
		}
	}
	

	$('#bodyDetail').append(tableData);
	$('#tableDetail').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ -1 ],
		[ 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			{
				extend: 'copy',
				className: 'btn btn-success',
				text: '<i class="fa fa-copy"></i> Copy',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'print',
				className: 'btn btn-warning',
				text: '<i class="fa fa-print"></i> Print',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true
	});

	var title = '<center><b><span style="color: red;">' + series.toUpperCase() + '</span> STOCKTAKING SURVEY<br>ON ' + month.toUpperCase() + '</b></center>';
	$('#modalDetailTitle').html(title);
	$('#modalDetail').modal('show');
	$('#loading').hide();
}

function showDetailChart2(series, month) {

	console.log(series+'_'+month);

	$('#loading').show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#bodyDetail').html("");
	var tableData = "";

	if(series == 'Sudah Menjawab'){
		for (var i = 0; i < surveys.length; i++) {

			var curr_emp = '';
			for (var j = 0; j < employees.length; j++) {
				if(surveys[i].employee_id == employees[j].employee_id){
					curr_emp = employees[j];
					break;
				}
			}

			if(curr_emp != ''){
				tableData += '<tr>';
				tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.employee_id +'</td>';
				tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.name +'</td>';
				tableData += '<td style="width:5%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.department_shortname +'</td>';
				tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.section +'</td>';
				tableData += '<td style="width:22.5%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.group +'</td>';
				tableData += '<td style="width:17.5%; padding:0px 5px 0px 5px; text-align:center;">'+ surveys[i].remark +'</td>';
				tableData += '</tr>';	
			}
		}

	}else{
		for (var j = 0; j < employees.length; j++) {
			var answer = false;
			for (var k = 0; k < surveys.length; k++) {
				if(surveys[k].employee_id == employees[j].employee_id){
					answer = true;
					break;
				}
			}

			if(answer){
				continue;
			}else{
				tableData += '<tr>';
				tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].employee_id +'</td>';
				tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].name +'</td>';
				tableData += '<td style="width:5%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].department_shortname +'</td>';
				tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].section +'</td>';
				tableData += '<td style="width:22.5%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].group +'</td>';
				tableData += '<td style="width:17.5%; padding:0px 5px 0px 5px; text-align:center;">BELUM MENJAWAB</td>';
				tableData += '</tr>';
			}
		}

	}
	

	$('#bodyDetail').append(tableData);
	$('#tableDetail').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ -1 ],
		[ 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			{
				extend: 'copy',
				className: 'btn btn-success',
				text: '<i class="fa fa-copy"></i> Copy',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'print',
				className: 'btn btn-warning',
				text: '<i class="fa fa-print"></i> Print',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true
	});

	var title = '<center><b><span style="color: red;">' + series.toUpperCase() + '</span> STOCKTAKING SURVEY<br>ON ' + month.toUpperCase() + '</b></center>';
	$('#modalDetailTitle').html(title);
	$('#modalDetail').modal('show');
	$('#loading').hide();
}

function showDetailChart1(category, series, month) {

	console.log(category+'_'+series+'_'+month);

	$('#loading').show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#bodyDetail').html("");
	var department = "";
	var tableData = "";

	if(series != 'Belum Menjawab'){
		for (var i = 0; i < surveys.length; i++) {

			var curr_emp = '';
			for (var j = 0; j < employees.length; j++) {
				if(surveys[i].employee_id == employees[j].employee_id){
					curr_emp = employees[j];
					break;
				}
			}

			if(curr_emp != ''){
				if(curr_emp.department_shortname == category && series.toUpperCase() == surveys[i].remark.toUpperCase()){
					department = curr_emp.department;					

					tableData += '<tr>';
					tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.employee_id +'</td>';
					tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.name +'</td>';
					tableData += '<td style="width:5%; padding:0px 5px 0px 5px; text-align:center;">'+ curr_emp.department_shortname +'</td>';
					tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.section +'</td>';
					tableData += '<td style="width:22.5%; padding:0px 5px 0px 5px; text-align:left;">'+ curr_emp.group +'</td>';
					tableData += '<td style="width:15%; padding:0px 5px 0px 5px; text-align:center;">'+ surveys[i].remark +'</td>';
					tableData += '</tr>';
				}
			}
			
		}

	}else{
		for (var j = 0; j < employees.length; j++) {
			var answer = false;
			for (var k = 0; k < surveys.length; k++) {
				if(surveys[k].employee_id == employees[j].employee_id){
					answer = true;
					break;
				}
			}

			if(answer){
				continue;
			}else{
				if(employees[j].department_shortname == category){
					department = employees[j].department;					

					tableData += '<tr>';
					tableData += '<td style="width:10%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].employee_id +'</td>';
					tableData += '<td style="width:20%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].name +'</td>';
					tableData += '<td style="width:5%; padding:0px 5px 0px 5px; text-align:center;">'+ employees[j].department_shortname +'</td>';
					tableData += '<td style="width:25%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].section +'</td>';
					tableData += '<td style="width:22.5%; padding:0px 5px 0px 5px; text-align:left;">'+ employees[j].group +'</td>';
					tableData += '<td style="width:15%; padding:0px 5px 0px 5px; text-align:center;">BELUM MENJAWAB</td>';
					tableData += '</tr>';
				}
			}
		}

	}
	

	$('#bodyDetail').append(tableData);
	$('#tableDetail').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ -1 ],
		[ 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			{
				extend: 'copy',
				className: 'btn btn-success',
				text: '<i class="fa fa-copy"></i> Copy',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			{
				extend: 'print',
				className: 'btn btn-warning',
				text: '<i class="fa fa-print"></i> Print',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': false,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true
	});

	var title = '<center><b>' + department.toUpperCase() + '<br><span style="color: red;">' + series.toUpperCase() + '</span> STOCKTAKING SURVEY<br>ON ' + month.toUpperCase() + '</b></center>';
	$('#modalDetailTitle').html(title);
	$('#modalDetail').modal('show');
	$('#loading').hide();
}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
			]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);


function openSuccessGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url('images/image-screen.png') }}',
		sticky: false,
		time: '5000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url('images/image-stop.png') }}',
		sticky: false,
		time: '5000'
	});
}

</script>
@endsection