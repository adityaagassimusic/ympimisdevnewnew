@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}
	</h1>
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#generateModal" class="btn btn-success btn-sm" style="color: white;">
				&nbsp;<i class="fa fa-refresh"></i>&nbsp;Generate Schedule
			</a>
			<a data-toggle="modal" data-target="#info" class="btn btn-primary btn-sm" style="color: white;">
				&nbsp;<i class="fa fa-gear"></i>&nbsp;Change Capacity
			</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-2">
			<div class="input-group date pull-right" style="text-align: center;">
				<div class="input-group-addon bg-green">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control monthpicker" name="month" id="month" placeholder="Select Month">  
			</div>
		</div>

		<div class="col-xs-2" style="padding: 0px;">
			<button onclick="fillTable()" class="btn btn-primary">Search</button>
		</div>
	</div>

	<div class="col-xs-12" style="padding: 0px; margin-top: 2%;">
		<div class="box" style="padding: 10px;">
			<div class="box-body" style="overflow-x: auto; padding: 0px;">
				<h3 style="margin-top: 0px;">CLARINET <span class="pull-right month_schedule"></span></h3>
				<table id="tableCLFG" class="table table-bordered" style="width: 100%; font-size: 12px;">
					<thead id="tableHeadCLFG" style="background-color: rgba(126,86,134,.7);">
						<th></th>
					</thead>
					<tbody id="tableBodyCLFG">
						<td></td>
					</tbody>
					<tbody id="tableFootCLFG">
						<td></td>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="col-xs-12" style="padding: 0px; margin-top: 1%;">
		<div class="box" style="padding: 10px;">
			<div class="box-body" style="overflow-x: auto; padding: 0px;">
				<h3 style="margin-top: 0px;">ALTO SAXOPHONE <span class="pull-right month_schedule"></span></h3>
				<table id="tableASFG" class="table table-bordered" style="width: 100%; font-size: 12px;">
					<thead id="tableHeadASFG" style="background-color: rgba(126,86,134,.7);">
						<th></th>
					</thead>
					<tbody id="tableBodyASFG">
						<td></td>
					</tbody>
					<tbody id="tableFootASFG">
						<td></td>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">Generate Production Schedule</h4>
				Generate Production Schedule akan mengahapus schedule yang telah ada<br>
				Production Schedule yang dihapus tidak dapat dikembalikan<br>
				Dan akan diganti dengan hasil generate dari Production Schedule yang terbaru<br>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-6 col-xs-offset-3">
						<div class="col-xs-12">
							<label>Select Month</label>
							<div class="input-group date pull-right" style="text-align: center;">
								<div class="input-group-addon bg-green">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control monthpicker" id="generate_month" placeholder="Select Month">  
							</div>
						</div>
						<div class="col-xs-12" style="margin-top: 3%;">
							<label>Select HPL</label>
							<select class="form-control select2" multiple="multiple" id='generate_hpl' style="width: 100%;">
								<option value="FLFG">FLFG</option>
								<option value="CLFG">CLFG</option>
								<option value="ASFG">ASFG</option>
								<option value="TSFG">TSFG</option>
							</select>
						</div>
					</div>    
				</div>
			</div>
			<div class="modal-footer">
				<div class="row" style="margin-top: 7%; margin-right: 2%;">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button onclick="generate()" class="btn btn-success">Generate</button>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

		$('.select2').select2({
			allowClear: true,
		});


		$('body').toggleClass("sidebar-collapse");

		fillTable();

	});

	var calendars = [];
	var forecasts = [];
	var materials = [];
	var schedules = [];
	var capacities = [];

	var resumeBaseModelDay = [];
	var resumeForecastBaseModel = [];

	function fillTable(){
		var data = {
			month : $('#month').val()
		}

		$('#loading').show();

		$.get('{{ url("fetch/view_production_schedule_bi") }}', data, function(result, status, xhr){
			if(result.status){
				$('.month_schedule').text(result.month);				
				calendars = result.calendars;
				forecasts = result.forecasts;
				materials = result.materials;
				schedules = result.schedules;
				capacities = result.capacities;

				resumeSchedule();

				showSchedule('CLFG');
				showSchedule('ASFG');

				$('#loading').hide();

			}

		});

	}

	function resumeSchedule() {

		//Resume Base Model per Day
		resumeBaseModelDay = [];
		var temp = [];
		for (var i = 0; i < schedules.length; i++) {
			var key = schedules[i].hpl + '#' + schedules[i].base_model + '#' + schedules[i].due_date;

			if(!temp[key]) {
				temp[key] = {
					'hpl' : schedules[i].hpl,
					'base_model' : schedules[i].base_model,
					'due_date' : schedules[i].due_date,
					'quantity' : schedules[i].quantity
				};
			} else {
				temp[key].quantity += schedules[i].quantity;
			}
		}

		for (var key in temp) {
			resumeBaseModelDay.push(temp[key]);
		}


		//Resume Forecast per Base Model
		resumeForecastBaseModel = [];
		var temp = [];
		for (var i = 0; i < forecasts.length; i++) {

			var base_model;
			for (var j = 0; j < materials.length; j++) {
				if(materials[j].material_number == forecasts[i].material_number){
					base_model = materials[j].base_model;
					break;
				}
			}

			var key = forecasts[i].hpl + '#' + base_model;

			if(!temp[key]) {
				temp[key] = {
					'hpl' : forecasts[i].hpl,
					'base_model' : base_model,
					'quantity' : forecasts[i].quantity
				};
			} else {
				temp[key].quantity += forecasts[i].quantity;
			}
		}

		for (var key in temp) {
			resumeForecastBaseModel.push(temp[key]);
		}

	}

	function showSchedule(hpl) {
		$('#table' + hpl).DataTable().clear();
		$('#table' + hpl).DataTable().destroy();
		$('#tableHead' + hpl).html("");

		var tableHead = '<tr>';
		tableHead += '<th>GMC</th>';
		tableHead += '<th>DESC</th>';
		tableHead += '<th>CAPACITY</th>';
		for (var i = 0; i < calendars.length; i++) {
			tableHead += '<th>'+ calendars[i].week_date.slice(8) +'</th>';
		}
		tableHead += '<th>TOTAL</th>';
		tableHead += '<th>FORECAST</th>';
		tableHead += '<th>DIFF</th>';
		tableHead += '</tr>';
		$('#tableHead' + hpl).append(tableHead);



		$('#tableBody' + hpl).html("");
		var tableBody = '';

		//MAIN
		for (var i = 0; i < materials.length; i++) {
			if(materials[i].hpl == hpl){
				var css_left = "background-color : #fcfaf1; vertical-align: middle;";
				tableBody += '<tr>';
				tableBody += '<td style="'+css_left+'">'+materials[i].material_number+'</td>';
				tableBody += '<td style="'+css_left+'">'+materials[i].material_description+'</td>';
				tableBody += '<td style="'+css_left+'">-</td>';
				
				//SCHEDULE
				var sum_row = 0;
				for (var j = 0; j < calendars.length; j++) {
					var inserted_schedule = false;
					var css_body = 'text-align: center;';
					if(calendars[j].remark == 'H'){
						css_body += "background-color: gainsboro;";
					}

					for (var k = 0; k < schedules.length; k++) {
						if((schedules[k].material_number == materials[i].material_number) && (schedules[k].due_date == calendars[j].week_date)){
							tableBody += '<th style="'+css_body+'">'+schedules[k].quantity+'</th>';
							sum_row += schedules[k].quantity;
							inserted_schedule = true;
						}
					}
					
					if(!inserted_schedule){
						tableBody += '<th style="'+css_body+'">0</th>';
					}
				}

				//RESUME
				tableBody += '<th style="text-align: right; background-color : #fcfaf1;">'+sum_row+'</th>';

				//FORECAST
				var inserted_forecast = false;
				var forecast = 0;
				for (var x = 0; x < forecasts.length; x++) {
					if(forecasts[x].material_number == materials[i].material_number){
						tableBody += '<th style="'+css_left+' text-align: right;">'+forecasts[x].quantity+'</th>';
						forecast = forecasts[x].quantity;
						inserted_forecast = true;
					}
				}

				if(!inserted_forecast){
					tableBody += '<th style="'+css_left+' text-align: right;">0</th>';
				}

				var diff = sum_row - forecast;
				var css_diff = 'text-align: right;';
				if(diff == 0){
					css_diff += 'background-color : #fcfaf1;';
				}else if(diff > 0){
					css_diff += 'background-color : #ffccff;';
					diff = '+' + diff;
				}else{
					css_diff += 'background-color : #ffccff;';
				}

				tableBody += '<th style="'+css_diff+'">'+diff+'</th>';
				tableBody += '</tr>';
			}
		}

		$('#tableBody' + hpl).append(tableBody);



		//RESUME
		// var colspan = 3 + calendars.length;
		// tableBody += '</tr>';
		// tableBody += '<th colspan="2" style="padding-bottom: 0px;">&nbsp;</th>';
		// tableBody += '<th style="padding-bottom: 0px;">CAPACITY</th>';
		// tableBody += '<th colspan="'+colspan+'" style="padding-bottom: 0px;">&nbsp;</th>';
		// tableBody += '</tr>';


		$('#tableFoot' + hpl).html("");
		var tableFoot = ''
		var total_capacity = 0;	
		for (var i = 0; i < capacities.length; i++) {
			if(capacities[i].hpl == hpl){
				var css_left = "background-color : #fcfaf1; vertical-align: middle; vertical-align: middle;";
				tableFoot += '<tr>';
				tableFoot += '<td style="'+css_left+'" colspan="2">'+capacities[i].base_model+'</td>';
				tableFoot += '<td style="'+css_left+' width: 1%;">'+capacities[i].quantity+'</td>';
				total_capacity += capacities[i].quantity;
				var capacity = capacities[i].quantity;

				//SCHEDULE
				var sum_row = 0;
				for (var j = 0; j < calendars.length; j++) {

					var inserted_resume_day = false;
					var css_body = 'text-align: center;';
					if(calendars[j].remark == 'H'){
						css_body += "background-color: gainsboro;";
					}

					for (var k = 0; k < resumeBaseModelDay.length; k++) {
						if((resumeBaseModelDay[k].base_model == capacities[i].base_model) && (resumeBaseModelDay[k].due_date == calendars[j].week_date) && (resumeBaseModelDay[k].hpl == capacities[i].hpl)){
							if(resumeBaseModelDay[k].quantity > capacity){
								css_body += "background-color: #ffccff;";
							}else{
								if(calendars[j].remark == 'H'){
									css_body += "background-color: gainsboro;";
								}	
							}

							tableFoot += '<th style="'+css_body+'">'+resumeBaseModelDay[k].quantity+'</th>';
							sum_row += resumeBaseModelDay[k].quantity;
							inserted_resume_day = true;
						}
					}
					
					if(!inserted_resume_day){
						tableFoot += '<th style="'+css_body+'">0</th>';
					}

				}

				tableFoot += '<th style="text-align: right; background-color: #fcfaf1;">'+sum_row+'</th>';

				var	forecast = 0;
				var inserted_resume_forecast = false;
				for (var j = 0; j < resumeForecastBaseModel.length; j++) {
					if((resumeForecastBaseModel[j].base_model == capacities[i].base_model) && (capacities[i].hpl == resumeForecastBaseModel[j].hpl)){
						tableFoot += '<th style="text-align: right; background-color: #fcfaf1;">'+resumeForecastBaseModel[j].quantity+'</th>';
						forecast = resumeForecastBaseModel[j].quantity;
						inserted_resume_forecast = true;
					}
				}

				if(!inserted_resume_forecast){
					tableFoot += '<th style="text-align: right; background-color: #fcfaf1;">0</th>';
				}

				var diff = sum_row - forecast;
				var css_diff = 'text-align: right;';
				if(diff == 0){
					css_diff += 'background-color : #fcfaf1;';
				}else if(diff > 0){
					css_diff += 'background-color : #ffccff;';
					diff = '+' + diff;
				}else{
					css_diff += 'background-color : #ffccff;';
				}

				tableFoot += '<th style="'+css_diff+'">'+diff+'</th>';
				tableFoot += '</tr>';


			}
		}

		tableFoot += '<tr>';
		tableFoot += '<td style="text-align: center; background-color: #fcfaf1; vertical-align: middle;" colspan="2">TOTAL</td>';
		tableFoot += '<td style="text-align: center; background-color: #fcfaf1; vertical-align: middle;">'+total_capacity+'</td>';

		var sum_total = 0;
		for (var i = 0; i < calendars.length; i++) {
			var css_body = 'text-align: center;';

			var sum_day = 0;
			for (var j = 0; j < resumeBaseModelDay.length; j++) {
				if((resumeBaseModelDay[j].due_date == calendars[i].week_date) && (resumeBaseModelDay[j].hpl == hpl) ){
					sum_day += resumeBaseModelDay[j].quantity;
					sum_total += resumeBaseModelDay[j].quantity;
				}
			}

			if(sum_day > total_capacity){
				css_body += "background-color: #ffccff;";
			}else{
				if(calendars[i].remark == 'H'){
					css_body += "background-color: gainsboro;";
				}	
			}

			tableFoot += '<th style="'+css_body+'">'+sum_day+'</th>';
		}
		tableFoot += '<th style="text-align: right; background-color: #fcfaf1;">'+sum_total+'</th>';


		var sum_forecast = 0;
		for (var i = 0; i < resumeForecastBaseModel.length; i++) {
			if(resumeForecastBaseModel[i].hpl == hpl){
				sum_forecast += resumeForecastBaseModel[i].quantity;				
			}
		}
		tableFoot += '<th style="text-align: right; background-color: #fcfaf1;">'+sum_forecast+'</th>';

		var diff = sum_total - sum_forecast;
		var css_diff = 'text-align: right;';
		if(diff == 0){
			css_diff += 'background-color : #fcfaf1;';
		}else if(diff > 0){
			css_diff += 'background-color : #ffccff;';
			diff = '+' + diff;
		}else{
			css_diff += 'background-color : #ffccff;';
		}

		tableFoot += '<th style="'+css_diff+'">'+diff+'</th>';
		tableFoot += '</tr>';

		$('#tableFoot' + hpl).append(tableFoot);


		$('#table' + hpl).DataTable({
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
			'paging': false,
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

	}

	function generate() {

		var month = $('#generate_month').val();
		var hpl = $('#generate_hpl').val();

		var data = {
			month : month,
			hpl : hpl,
		}

		if(hpl.length < 1 || month == ''){
			openErrorGritter("Error", "Select Month & HPL");
			return false;
		}

		$("#loading").show();
		$.post('{{ url("generate/production_schedule_bi") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();

				$('#generate_month').val('');
				$('#generate_hpl').val('');
				$("#generate_hpl").trigger("change");

				$('#generateModal').modal('hide');

				openSuccessGritter('Success', 'Adjusment Success');
			}else{
				$("#loading").hide();
				openErrorGritter('Error', 'Adjusment Failed');
			}
		});
	}

	function clearConfirmation(){
		location.reload(true);    
	}

	$('#generateModal').on('hidden.bs.modal', function () {
		$('#generate_month').val('');
		$('#generate_hpl').val('');
		$("#generate_hpl").trigger("change");
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

</script>
@endsection