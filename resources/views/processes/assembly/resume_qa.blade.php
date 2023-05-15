@extends('layouts.master')
@section('stylesheets')
<?php use \App\Http\Controllers\AssemblyProcessController; ?>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-xs-2" style="padding-right: 5px;">
							<div class="input-group date">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
							</div>
						</div>

						<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
							<div class="input-group date">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
							</div>
						</div>
						<div class="col-xs-2" style="padding-left: 0px;">
							<button class="btn btn-success" onclick="fillData()" style="width: 100%">
								Search
							</button>
						</div>
						<div class="col-xs-6" style="text-align: right;">
							<span style="font-weight: bold;font-size: 23px;color: white;padding: 10px;color: black" id="dateTitle">2022-10-01</span>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-xs-4">
							<table id="tableResume" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);color: white;">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">NG Name</th>
										<th style="width: 2%;text-align: right;">Qty NG (Pcs)</th>
										<th style="width: 2%;text-align: right;">Qty NG (Set)</th>
										<th style="width: 2%;text-align: right;">% NG (Pareto)</th>
										<th style="width: 2%;text-align: right;">% NG (Perolehan)</th>
									</tr>
								</thead>
								<tbody id="bodyTableResume">
								</tbody>
							</table>
						</div>
						<div class="col-xs-8">
							<div id="container" style="height: 60vh">
								
							</div>
							<div id="container2" style="height: 60vh">
								
							</div>
							<div>
								<table id="tablePass" class="table table-bordered table-striped table-hover">
									<thead style="background-color: rgba(126,86,134,.7);color: white" id="headTablePass">
									</thead>
									<tbody id="bodyTablePass">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Operator</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Operator<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditOperator">
										<select class="form-control select3" data-placeholder="Select Operator" name="edit_operator" id="edit_operator" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="edit_tag" class="form-control" id="edit_tag" placeholder="Tap ID Card Operator" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditLocation">
										<select class="form-control select3" data-placeholder="Select Location" name="edit_location" id="edit_location" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Line<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditLine">
										<select class="form-control select3" data-placeholder="Select Line" name="edit_line" id="edit_line" style="width: 100%">
											<option value=""></option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/highcharts.js')}}"></script>
<script src="{{ url('js/pareto.js')}}"></script>
<script src="{{ url('js/exporting.js')}}"></script>
<script src="{{ url('js/export-data.js')}}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#date_from').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#date_to').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		
		fillData();
	});

	function fillData(){
		$('#loading').show();
		
		var data = {
			origin_group_code:'{{$origin_group_code}}',
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/assembly/resume_qa") }}',data, function(result, status, xhr){
			if(result.status){
				if (result.resumes != null) {
					// $('#tableResume').DataTable().clear();
					// $('#tableResume').DataTable().destroy();
					$('#bodyTableResume').html("");
					var tableResume = "";

					var ng_name = [];

					for(var i = 0; i < result.resumes.length;i++){
						if (result.resumes[i].ng_name != null) {
							if (result.resumes[i].ng_name.match(/,/gi)) {
								var ng_names = result.resumes[i].ng_name.split(',');
								for(var j = 0; j < ng_names.length;j++){
									ng_name.push(ng_names[j]);
								}
							}else{
								ng_name.push(result.resumes[i].ng_name);
							}
						}
					}
					var ng_unik = ng_name.filter(onlyUnique);

					var ng_count = [];
					var ng_count_set = [];

					for(var j = 0;j < ng_unik.length;j++){
						var count = 0;
						var count_set = 0;
						for(var i = 0; i < result.resumes.length;i++){
							if (result.resumes[i].ng_name != null) {
								var ngs = new RegExp(ng_unik[j], 'gi');
								if (result.resumes[i].ng_name.match(/,/gi)) {
									var ng_alls = result.resumes[i].ng_name.split(',');
									for(var k = 0; k < ng_alls.length;k++){
										if (ng_alls[k] == ng_unik[j]) {
											count++;
										}
									}
								}else{
									if (result.resumes[i].ng_name.match(ngs)) {
										count++;
									}
								}

								if (result.resumes[i].ng_name.match(ngs)) {
									count_set++;
								}
							}
						}
						ng_count.push({ng_name:ng_unik[j],qty:count});
						ng_count_set.push({ng_name:ng_unik[j],qty:count_set});
					}

					ng_count_set.sort(dynamicSortDesc('qty'));
					
					var index = 1;

					var total_set = 0;
					var total = 0;
					var total_pareto = 0;
					var total_paretos = [];
					var total_perolehan = [];
					var total_perolehans = [];

					var paretos = [];

					for(var i = 0; i < result.resumes.length;i++){
						total++;
					}

					$.each(ng_count_set, function(key, value) {
						tableResume += '<tr>';
						tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;">'+index+'</td>';
						tableResume += '<td style="border:1px solid black;text-align:left;padding-left:7px;">'+value.ng_name+'</td>';
						for(var i = 0; i < ng_count.length;i++){
							if (ng_count[i].ng_name == value.ng_name) {
								tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;">'+ng_count[i].qty+'</td>';
							}
						}
						total_pareto = total_pareto + parseInt(value.qty);
						total_paretos.push(total_pareto);
						total_perolehan.push({y:parseInt(value.qty),key:value.ng_name});
						total_perolehans.push(parseInt(value.qty));
						paretos.push(value.ng_name);
						total_set = total_set + parseInt(value.qty);
						tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;">'+value.qty+'</td>';
						tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;" id="pareto_'+index+'"></td>';
						tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;" id="perolehan_'+index+'"></td>';
						tableResume += '</tr>';
						index++;
					});
					tableResume += '<tr>';
					tableResume += '<td colspan="3" style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;">Total</td>';
					tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;">'+total_set+'</td>';
					tableResume += '<td colspan="2" style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;"></td>';
					tableResume += '</tr>';
					tableResume += '<tr>';
					tableResume += '<td colspan="3" style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;">Total Perolehan</td>';
					tableResume += '<td style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;">'+total+'</td>';
					tableResume += '<td colspan="2" style="border:1px solid black;text-align:right;padding-right:7px;background-color:lightyellow;font-weight:bold;"></td>';
					tableResume += '</tr>';
					$('#bodyTableResume').append(tableResume);
					var k = 1;
					for(var j = 0; j < total_paretos.length;j++){
						var pareto = ((total_paretos[j] / total_set)*100).toFixed(1);
						$('#pareto_'+(k)).html(pareto);
						k++;
					}

					var k = 1;
					for(var j = 0; j < total_perolehans.length;j++){
						var perolehan = ((total_perolehans[j] / total)*100).toFixed(1);
						$('#perolehan_'+(k)).html(perolehan);
						k++;
					}

					Highcharts.chart('container', {
					    chart: {
					        renderTo: 'container',
					        type: 'column'
					    },
					    title: {
					        text: 'PARETO DEFECT {{strtoupper($prod)}}',
					        style:{
					        	fontWeight:'bold',
					        	textTransform: 'uppercase',
								fontSize: '20px'
					        }
					    },
					    tooltip: {
					        shared: true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize: '10px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: '#adadad',
								borderWidth:'1'
								
							},
							pareto:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetailPareto(this.options.key);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}%',
									style:{
										fontSize: '10px'
									}
								},
								lineWidth: 3,
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
								
							},
						},credits: {
							enabled: false
						},
					    xAxis: {
					        categories: paretos,
					        crosshair: true
					    },
					    yAxis: [{
					        title: {
					            text: ''
					        },
					        
					        labels: {
					            format: "{value}"
					        }
					    }, {
					        title: {
					            text: ''
					        },
					        minPadding: 0,
					        maxPadding: 0,
					        max: 100,
					        min: 0,
					        opposite: true,
					        labels: {
					            format: "{value}%"
					        }
					    },],
					    series: [{
					        type: 'pareto',
					        name: 'Pareto',
					        yAxis: 1,
					        zIndex: 10,
					        baseSeries: 1,
					        tooltip: {
					            valueDecimals: 1,
					            valueSuffix: '%'
					        },
					        colorByPoint:false,
					        color:'#ff4f4f',
					    }, {
					        name: 'Qty NG (Set)',
					        type: 'column',
					        zIndex: 2,
					        data: total_perolehan,
					        colorByPoint:false,
					        color:'#00c0ef ',
					    },]
					});

					var month = [];
					var month_name = [];

					for(var i = 0; i < result.pass_ratio.length;i++){
						month.push(result.pass_ratio[i].month);
						month_name.push({month:result.pass_ratio[i].month,month_name:result.pass_ratio[i].month_name});
					}

					var month_unik = month.filter(onlyUnique);

					var categories = [];
					var totals = [];
					var oks = [];					

					for(var j = 0; j < month_unik.length;j++){
						var month_names = '';
						for(var i = 0; i < month_name.length;i++){
							if (month_name[i].month == month_unik[j]) {
								month_names = month_name[i].month_name;
							}
						}
						categories.push(month_names);
						var total = 0;
						var ok = 0;
						for(var k = 0; k < result.pass_ratio.length;k++){
							if (result.pass_ratio[k].month == month_unik[j]) {
								total++;
								if (result.pass_ratio[k].ng == null) {
									ok++;
								}
							}
						}
						totals.push(total);
						oks.push(ok);
					}

					var pass_ratio = [];
					for(var i = 0; i < totals.length;i++){
						pass_ratio.push(parseFloat(((parseInt(oks[i])/parseInt(totals[i]))*100).toFixed(1)));
					}


					Highcharts.chart('container2', {
						chart: {
							type: 'spline',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: 'PASS RATIO {{strtoupper($prod)}}',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: '#000',
							lineWidth:2,
							lineColor:'#000',
							labels: {
								style: {
									fontSize: '14px',
									fontWeight: 'bold'
								}
							},
						},
						yAxis: [{ // Secondary yAxis
							title: {
								text: 'Pass Ratio (%)',
								style: {
									color: '#000',
									fontSize: '18px',
									fontWeight: 'bold',
									fill: '#000'
								}
							},
							labels:{
								style:{
									fontSize:"14px"
								}
							},
							type: 'linear',
						}
						],
						tooltip: {
							headerFormat: '<span>Pass Ratio</span><br/>',
							pointFormat: '<b>{point.y:,.0f}%</b>',
						},
						legend: {
							layout: 'horizontal',
							itemStyle: {
								fontSize:'16px',
							},
						},	
						credits: {
							enabled: false
						},
						plotOptions: {
							series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,result.date,'ng_name');
										}
									}
								},
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '0.8vw'
									}
								},
								animation: {
									enabled: true,
									duration: 800
								},
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},
						series: [
						{
							type: 'spline',
							data: pass_ratio,
							name: 'Pass Ratio',
							colorByPoint: false,
							color:'#0ea135',
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}%' ,
								style:{
									fontSize: '1vw',
									textShadow: false,
									color:'black',
									borderWidth:0,
								},
							},
							
						},
						]
					});

					$('#headTablePass').html('');
					var headTable = '';

					headTable += '<tr>';
					headTable += '<th style="width:1%">#</th>';
					for(var j = 0; j < month_unik.length;j++){
						var month_names = '';
						for(var i = 0; i < month_name.length;i++){
							if (month_name[i].month == month_unik[j]) {
								month_names = month_name[i].month_name;
							}
						}
						headTable += '<th style="width:2%">'+month_names+'</th>';
					}
					headTable += '</tr>';

					$('#headTablePass').append(headTable);

					$('#bodyTablePass').html('');
					var bodyTable = '';

					bodyTable += '<tr>';
					bodyTable += '<td style="border:1px solid black;font-weight:bold">Pass Ratio</td>';
					for(var j = 0; j < month_unik.length;j++){
						bodyTable += '<td style="text-align:right;padding-right:7px;border:1px solid black;">'+pass_ratio[j]+'%</td>';
					}
					bodyTable += '</tr>';
					bodyTable += '<tr>';
					bodyTable += '<td style="border:1px solid black;font-weight:bold">Total Check</td>';
					for(var j = 0; j < month_unik.length;j++){
						bodyTable += '<td style="text-align:right;padding-right:7px;border:1px solid black;">'+totals[j]+'</td>';
					}
					bodyTable += '</tr>';
					bodyTable += '<tr>';
					bodyTable += '<td style="border:1px solid black;font-weight:bold">OK</td>';
					for(var j = 0; j < month_unik.length;j++){
						bodyTable += '<td style="text-align:right;padding-right:7px;border:1px solid black;">'+oks[j]+'</td>';
					}
					bodyTable += '</tr>';

					$('#bodyTablePass').append(bodyTable);

					// var table = $('#tableResume').DataTable({
					// 	'dom': 'Bfrtip',
					// 	'responsive':true,
					// 	'lengthMenu': [
					// 	[ 10, 25, 50, -1 ],
					// 	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					// 	],
					// 	'buttons': {
					// 		buttons:[
					// 		{
					// 			extend: 'pageLength',
					// 			className: 'btn btn-default',
					// 		},
					// 		{
					// 			extend: 'copy',
					// 			className: 'btn btn-success',
					// 			text: '<i class="fa fa-copy"></i> Copy',
					// 			exportOptions: {
					// 				columns: ':not(.notexport)'
					// 			}
					// 		},
					// 		{
					// 			extend: 'excel',
					// 			className: 'btn btn-info',
					// 			text: '<i class="fa fa-file-excel-o"></i> Excel',
					// 			exportOptions: {
					// 				columns: ':not(.notexport)'
					// 			}
					// 		},
					// 		{
					// 			extend: 'print',
					// 			className: 'btn btn-warning',
					// 			text: '<i class="fa fa-print"></i> Print',
					// 			exportOptions: {
					// 				columns: ':not(.notexport)'
					// 			}
					// 		}
					// 		]
					// 	},
					// 	'paging': true,
					// 	'lengthChange': true,
					// 	'pageLength': 10,
					// 	'searching': true,
					// 	"processing": true,
					// 	'ordering': true,
					// 	'order': [],
					// 	'info': true,
					// 	'autoWidth': true,
					// 	"sPaginationType": "full_numbers",
					// 	"bJQueryUI": true,
					// 	"bAutoWidth": false,
					// 	"processing": true
					// });
				}

				$('#loading').hide();

				$('#dateTitle').html(result.dateTitle);

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);


	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function dynamicSortDesc(property) {
	    var sortOrder = -1;
	    if(property[0] === "-") {
	        sortOrder = -1;
	        property = property.substr(1);
	    }
	    return function (a,b) {
	        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
	        return result * sortOrder;
	    }
	}

</script>
@endsection