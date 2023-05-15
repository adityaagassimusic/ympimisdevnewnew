@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		vertical-align: middle;
		padding: 2px;
	}
	tbody>tr>td{
		vertical-align: middle;
		padding: 2px;
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 0px;
	    margin-top: 10px;
	    display: inline-block;
	    border: 2px solid white;
	  }

	  #bodyTableCouncelingInitial > tbody > tr > td{
	  	vertical-align: middle;
	  }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1vw;color: white"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div id="container1" class="gambar" style="width: 100%;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalCouncelingInitial">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #03adfc;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling">TRAINING DAN KONSELING</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<table class="table table-hover table-bordered table-striped">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Check Type</th>
									<th style="width: 1%;">ID</th>
									<th style="width: 2%;">Name</th>
									<th style="width: 2%;">Tag</th>
									<th style="width: 1%;">Action</th>
								</tr>
							</thead>
							<tbody id="bodyTableCouncelingInitial">
							</tbody>
						</table>

			            <div class="form-group">
			              <div class="col-xs-12" style="padding-left: 0px;">
			              	<label for="">Document Training</label>
			              </div>
						  <div class="col-xs-10" style="padding-left: 0px;">
						  	<a href="{{url('input/pn/training_document/qa/initial')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
						  </div>
			            </div>
			            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
			            	<span style="padding: 20px;font-size: 20px">CREATE CAR</span>
			            </div>
			            <div class="col-xs-12" style="padding-top: 20px" id="div_reject_reason_initial">
			            	<div class="row">
			            		<div class="form-group">
									<label style="color: red">Reject Reason</label><br>
									<textarea id="reject_reason_initial" style="width: 100%" readonly></textarea>
								</div>
			            	</div>
			            </div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Deskripsi Initial</label><br>
									<textarea id="car_description_initial" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action Initial</label><br>
									<textarea id="car_action_now_initial" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause Initial</label><br>
									<textarea id="car_cause_initial" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action Initial</label><br>
									<textarea id="car_action_initial" style="width: 100%"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" id="btn_submit_initial">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCouncelingFinal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #03adfc;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling">TRAINING DAN KONSELING</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<table class="table table-hover table-bordered table-striped">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Check Type</th>
									<th style="width: 1%;">ID</th>
									<th style="width: 2%;">Name</th>
									<th style="width: 2%;">Tag</th>
									<th style="width: 1%;">Action</th>
								</tr>
							</thead>
							<tbody id="bodyTableCouncelingFinal">
							</tbody>
						</table>

			            <div class="form-group">
			              <div class="col-xs-12" style="padding-left: 0px;">
			              	<label for="">Document Training</label>
			              </div>
						  <div class="col-xs-10" style="padding-left: 0px;">
						  	<a href="{{url('input/pn/training_document/qa/final')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
						  </div>
			            </div>
			            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
			            	<span style="padding: 20px;font-size: 20px">CREATE CAR</span>
			            </div>
			            <div class="col-xs-12" style="padding-top: 20px" id="div_reject_reason_final">
			            	<div class="row">
			            		<div class="form-group">
									<label style="color: red">Reject Reason</label><br>
									<textarea id="reject_reason_final" style="width: 100%" readonly></textarea>
								</div>
			            	</div>
			            </div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Deskripsi Final</label><br>
									<textarea id="car_description_final" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action Final</label><br>
									<textarea id="car_action_now_final" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause Final</label><br>
									<textarea id="car_cause_final" style="width: 100%"></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action Final</label><br>
									<textarea id="car_action_final" style="width: 100%"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" id="btn_submit_final">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('#date_to').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 60000);

		$('#div_reject_reason_initial').hide();
		$('#div_reject_reason_final').hide();

		CKEDITOR.replace('reject_reason_initial' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('reject_reason_final' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
		CKEDITOR.replace('car_description_initial' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_initial' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_initial' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_initial' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('car_description_final' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_final' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_final' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_final' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: null,
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	var reject_reason_initial = '';
	var reject_reason_final = '';

	function fetchChart(){

		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/pn/display/qa_audit") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
				$('#container1').html('');
				var container = '';
				container += '<table class="table table-responsive" id="tableAudit" style="border:2px solid white;margin-bottom:0px" width="200px">';
				container += '<tr style="background-color:#159925">';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Date</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Auditor QA</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">PIC</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Product</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Defect</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px;">Area</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Category</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Image</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:20px">Training</th>';
				container += '</tr>';
				var index = 1;
				for(var i = 0; i < result.audit.length; i++){
					if (index %2 == 0) {
						var color = '#0072ab';
					}else{
						var color = '#124182';
					}
					// container += '<div style="width:200px">';
					var urlauditor = '{{ url("images/avatar/") }}/'+result.audit[i].auditor_id+'.jpg';
					// var urlauditee = '{{ url("images/avatar/") }}/'+result.audit[i].employee_id+'.jpg';
					var urlimage = '{{ url("data_file/pianica/qa_audit/") }}/'+result.audit[i].image;
					container += '<tr style="background-color:'+color+'">';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:30px">'+result.audit[i].check_date+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><div style="overflow: hidden;"><img style="width:100px;margin: -10% 0 0 0 !important;" src="'+urlauditor+'" class="user-image" alt="User image"></div><br><span style="font-size:25px">'+result.audit[i].auditor_id+'<br>'+result.audit[i].auditor_name.split(' ').slice(0,2).join(' ')+'</span></td>';
					// '+result.audit[i].employee_id+'<br>'+result.audit[i].employee_name.split(' ').slice(0,2).join(' ')+'
					var check_type = result.audit[i].check_type.split(',');
					var employee_id = result.audit[i].employee_id.split(',');
					var employee_name = result.audit[i].employee_name.split(',');
					var counceled_employee = result.audit[i].counceled_employee.split(',');
					var id = result.audit[i].id.split(',');
					var initial = [];
					var id_initial = [];
					var emp_initial = [];
					var emp_name_initial = [];
					var check_type_initial = [];
					var counceled_employee_initial = [];
					var final = [];
					var id_final = [];
					var emp_final = [];
					var emp_name_final = [];
					var check_type_final = [];
					var counceled_employee_final = [];

					var ada_initial = '';
					var ada_final = '';
					// <img style="width:100px" src="'+urlauditee+'" class="user-image" alt="User image">
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle">';
					for(var j = 0; j < check_type.length; j++){
						if (check_type[j] == 'Reed Adjustment' || check_type[j] == 'Tuning' || check_type[j] == 'Fixing Plate') {
							if (counceled_employee[j] == 'Belum') {
								initial.push(check_type[j]);
							}
							id_initial.push(id[j]);
							emp_initial.push(employee_id[j]);
							emp_name_initial.push(employee_name[j]);
							check_type_initial.push(check_type[j]);
							counceled_employee_initial.push(counceled_employee[j]);
							ada_initial = 'ada';
						}else{
							if (counceled_employee[j] == 'Belum') {
								final.push(check_type[j]);
							}
							id_final.push(id[j]);
							emp_final.push(employee_id[j]);
							emp_name_final.push(employee_name[j]);
							check_type_final.push(check_type[j]);
							counceled_employee_final.push(counceled_employee[j]);
							ada_final = 'ada';
						}
						container += '<div class="col-xs-6">';
						var urlauditee = '{{ url("images/avatar/") }}/'+employee_id[j]+'.jpg';
						container += check_type[j]+'<br>';
						container += '<div style="overflow: hidden;">';
						container += '<img style="width:90px;margin: -10% 0 0 0 !important;" src="'+urlauditee+'" class="user-image" alt="User image">';
						container += '</div>';
						container += employee_id[j]+'<br>'+employee_name[j].split(' ').slice(0,2).join(' ');
						container += '</div>';
					}
					container += '</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:30px;padding-right:0px;padding-left:0px;">'+result.audit[i].product.split(' - ')[1]+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:30px;">'+result.audit[i].defect+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:30px;">'+result.audit[i].area+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:30px;">'+result.audit[i].category+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:150px" src="'+urlimage+'" class="user-image" alt="User image"></td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle">';
					if (initial.length > 0 && counceled_employee_initial.includes('Belum') && ada_initial != '') {
						container += '<button class="btn btn-warning btn-lg"  style="font-weight:bold;font-size:20px;margin-bottom:20px;" onclick="councelingModalInitial(\''+id_initial+'\',\''+emp_initial+'\',\''+emp_name_initial+'\',\''+check_type_initial+'\')">TRAINING <br>& KONSELING <br>INITIAL</button><br>';
						if (result.audit[i].reject_reason != null) {
							reject_reason_initial = result.audit[i].reject_reason;
						}
					}else if(initial.length == 0 && !counceled_employee_initial.includes('Belum') && ada_initial != ''){
						var url = '{{url("print/pn/qa_audit")}}/initial/'+id_initial.join(',');
						container += '<a target="_blank" class="btn btn-success btn-lg" href="'+url+'" style="font-weight:bold;font-size:20px">HASIL TRAINING <br>& KONSELING <br>INITIAL</a><br>';
					}
					if (final.length > 0 && counceled_employee_final.includes('Belum') && ada_final != '') {
						container += '<button class="btn btn-primary btn-lg"  style="font-weight:bold;font-size:20px;margin-bottom:20px;" onclick="councelingModalFinal(\''+id_final+'\',\''+emp_final+'\',\''+emp_name_final+'\',\''+check_type_final+'\')">TRAINING <br>& KONSELING <br>FINAL</button><br>';
						if (result.audit[i].reject_reason != null) {
							reject_reason_final = result.audit[i].reject_reason;
						}
					}else if(final.length == 0 && !counceled_employee_final.includes('Belum') && ada_final != ''){
						var url = '{{url("print/pn/qa_audit")}}/final/'+id_final.join(',');
						container += '<a target="_blank" class="btn btn-success btn-lg" href="'+url+'" style="font-weight:bold;font-size:20px">HASIL TRAINING <br>& KONSELING <br>FINAL</a><br>';
					}
					container += '</td>';
					container += '</tr>';
					// container += '</div>';
					index++;
				}
				container += '</table>';
				$('#container1').append(container);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function submitCouncel(param,id) {
	$('#loading').show();
	if (param === 'initial') {
		var id = id.split(',');
		for(var i = 0; i < id.length; i++){
			if ($('#tag_initial_'+i).val() == "") {
				$('#loading').hide();
				openErrorGritter('Error!','Semua Data Harus Diisi');
				return false;
			}
		}
		if (CKEDITOR.instances.car_description_initial.getData() == ""
			|| CKEDITOR.instances.car_action_now_initial.getData() == ""
			|| CKEDITOR.instances.car_cause_initial.getData() == ""
			|| CKEDITOR.instances.car_action_initial.getData() == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Semua Data Harus Diisi');
			return false;
		}
		var counceled_employee = [];
		for(var i = 0; i < id.length; i++){
			counceled_employee.push($("#tag_initial_"+i).val());
		}


		var description_initial = CKEDITOR.instances.car_description_initial.getData();
		var action_now_initial = CKEDITOR.instances.car_action_now_initial.getData();
		var cause_initial = CKEDITOR.instances.car_cause_initial.getData();
		var action_initial = CKEDITOR.instances.car_action_initial.getData();

		var formData = new FormData();
		formData.append('counceled_employee', counceled_employee);
		formData.append('id_audit', id);
		formData.append('description_initial',description_initial);
		formData.append('action_now_initial',action_now_initial);
		formData.append('cause_initial',cause_initial);
		formData.append('action_initial',action_initial);
		formData.append('param',param);
	}else{
		var id = id.split(',');
		for(var i = 0; i < id.length; i++){
			if ($('#tag_final_'+i).val() == "") {
				$('#loading').hide();
				openErrorGritter('Error!','Semua Data Harus Diisi');
				return false;
			}
		}
		if (CKEDITOR.instances.car_description_final.getData() == ""
			|| CKEDITOR.instances.car_action_now_final.getData() == ""
			|| CKEDITOR.instances.car_cause_final.getData() == ""
			|| CKEDITOR.instances.car_action_final.getData() == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Semua Data Harus Diisi');
			return false;
		}
		var counceled_employee = [];
		for(var i = 0; i < id.length; i++){
			counceled_employee.push($("#tag_final_"+i).val());
		}

		var description_final = CKEDITOR.instances.car_description_final.getData();
		var action_now_final = CKEDITOR.instances.car_action_now_final.getData();
		var cause_final = CKEDITOR.instances.car_cause_final.getData();
		var action_final = CKEDITOR.instances.car_action_final.getData();

		var formData = new FormData();
		formData.append('counceled_employee', counceled_employee);
		formData.append('id_audit', id);
		formData.append('description_final',description_final);
		formData.append('action_now_final',action_now_final);
		formData.append('cause_final',cause_final);
		formData.append('action_final',action_final);
		formData.append('param',param);
	}

	console.log(formData);

	$.ajax({		
		url:"{{ url('input/pn/counceling') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			$('#loading').hide();
			fetchChart();
			$('#modalCouncelingInitial').modal('hide');
			$('#modalCouncelingFinal').modal('hide');
			openSuccessGritter('Success','Input Konseling Berhasil');
		},
		error: function (err) {
			$('#loading').hide();
	        openErrorGritter('Error!',err);
	    }
	})
}

function councelingModalInitial(id,emp,name,check_type) {
	$('#modalCouncelingInitial').modal('show');

	$('#bodyTableCouncelingInitial').html('');

	var bodyCounceling = '';

	var id = id.split(',');
	var emp = emp.split(',');
	var name = name.split(',');
	var check_type = check_type.split(',');

	for(var i = 0; i < id.length; i++){
		var initial = 'tag_initial_'+i;
		bodyCounceling += '<tr>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+check_type[i]+'</td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+emp[i]+'</td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+name[i]+' <input type="hidden" value="'+id[i]+'" id="id_'+i+'"></td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;"><input class="form-control" onkeyup="scanIdCard(this.id,event)" id="tag_initial_'+i+'" style="width:100%" placeholder="Scan ID Card '+check_type[i]+'"></td>';
		bodyCounceling += '<td style="text-align:center"><button class="btn btn-danger" onclick="cancelScan(\''+initial+'\')">Cancel</button></td>';
		bodyCounceling += '</tr>';
	}

	$('#bodyTableCouncelingInitial').append(bodyCounceling);

	var elemm = document.getElementById('btn_submit_initial');

	elemm.setAttribute("onclick",'submitCouncel("initial",\''+id+'\');');

	$('#div_reject_reason_initial').hide();
	if (reject_reason_initial != '') {
		$("#reject_reason_initial").html(CKEDITOR.instances.reject_reason_initial.setData(reject_reason_initial));
		$('#div_reject_reason_initial').show();
	}

	// $('#btn_submit_initial').prop('onclick','submitCouncel("initial",\''+id+'\')');

}

function councelingModalFinal(id,emp,name,check_type) {
	$('#modalCouncelingFinal').modal('show');

	$('#bodyTableCouncelingFinal').html('');

	var bodyCounceling = '';

	var id = id.split(',');
	var emp = emp.split(',');
	var name = name.split(',');
	var check_type = check_type.split(',');

	for(var i = 0; i < id.length; i++){
		var final = 'tag_final_'+i;
		bodyCounceling += '<tr>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+check_type[i]+'</td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+emp[i]+'</td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;">'+name[i]+' <input type="hidden" value="'+id[i]+'" id="id_'+i+'"></td>';
		bodyCounceling += '<td style="vertical-align:middle;padding:2px;"><input class="form-control" onkeyup="scanIdCard(this.id,event)" id="tag_final_'+i+'" style="width:100%" placeholder="Scan ID Card '+check_type[i]+'"></td>';
		bodyCounceling += '<td style="text-align:center"><button class="btn btn-danger" onclick="cancelScan(\''+final+'\')">Cancel</button></td>';
		bodyCounceling += '</tr>';
	}

	$('#bodyTableCouncelingFinal').append(bodyCounceling);

	var elemm = document.getElementById('btn_submit_final');

	elemm.setAttribute("onclick",'submitCouncel("final",\''+id+'\');');

	$('#div_reject_reason_final').hide();
	if (reject_reason_final != '') {
		$("#reject_reason_final").html(CKEDITOR.instances.reject_reason_final.setData(reject_reason_final));
		$('#div_reject_reason_final').show();
	}
}

function cancelScan(btn) {
	$('#'+btn).val('');
	$('#'+btn).removeAttr('disabled');
	$('#'+btn).focus();
}

function scanIdCard(id,param) {
	if (param.keyCode == 13 || param.keyCode == 9) {
		// $('#'+id).keyup(function(param) {
			if($("#"+id).val().length >= 8){
				var data = {
					employee_id : $("#"+id).val()
				}
				
				$.get('{{ url("scan/pn/qa_audit") }}', data, function(result, status, xhr){
					if(result.status){
						$("#"+id).val(result.emp.employee_id+' - '+result.emp.name);
						$('#'+id).prop('disabled',true);
						openSuccessGritter('Success!', result.message);
					}else{
						$('#'+id).removeAttr('disabled');
						$('#'+id).val('');
						$('#'+id).focus();
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				});
			}else{
				$('#'+id).removeAttr('disabled');
				$('#'+id).val('');
				$('#'+id).focus();
				audio_error.play();
				openErrorGritter('Error!','Tag Invalid');
			}
		// })
	}
}

function ShowModal(cat,date,type) {
	$("#loading").show();
	var location = $('#location').val();
	var origin = $('#origin').val();
	var data = {
		cat:cat,
		date:date,
		type:type,
		location:location,
		origin:origin,
	}

	$.get('{{ url("fetch/assembly/ng_rate_detail") }}', data, function(result, status, xhr) {
		if(result.status){
			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();
			$('#tableDetailBody').html('');
			var tableBody = '';
			var index = 1;
			var total_qty = 0;
			$.each(result.detail, function(key, value) {
				tableBody += '<tr>';
				tableBody += '<td>'+index+'</td>';
				tableBody += '<td>'+value.serial_number+'</td>';
				tableBody += '<td>'+value.model+'</td>';
				tableBody += '<td>'+value.location+'</td>';
				tableBody += '<td>'+value.ng_name+'</td>';
				tableBody += '<td>'+value.ongko+'</td>';
				var qty = 1;
				if (value.value_bawah == null) {
					tableBody += '<td>'+qty+'</td>';
					tableBody += '<td></td>';
					tableBody += '<td></td>';
				}else{
					tableBody += '<td>'+qty+'</td>';
					tableBody += '<td>'+value.value_bawah+'</td>';
					tableBody += '<td>'+value.value_atas+'</td>';
				}
				tableBody += '<td>'+(value.value_lokasi || "")+'</td>';
				tableBody += '<td>'+value.employee_id+'<br>'+value.name+'</td>';
				tableBody += '<td>'+value.created+'</td>';
				tableBody += '</tr>';
				total_qty++;
				index++;
			});
			$('#tableDetailBody').append(tableBody);
			$('#total_all').html(total_qty);

			var table = $('#tableDetail').DataTable({
				'dom': 'Bfrtip',
				'responsive':true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'buttons': {
					buttons:[
					{
						extend: 'pageLength',
						className: 'btn btn-default',
					},
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});
			if (type === 'ng_name') {
				$('#modalDetailTitle').html('Detail NG '+cat+'<br>Tanggal '+date);
			}else{
				$('#modalDetailTitle').html('Detail NG Pada Model '+cat+'<br>Tanggal '+date);
			}
			$("#loading").hide();
			$('#modalDetail').modal('show');
		}else{
			$("#loading").hide();
			openErrorGritter('Error!',result.message);
		}
	});
}

$.date = function(dateObject) {
	var d = new Date(dateObject);
	var day = d.getDate();
	var month = d.getMonth() + 1;
	var year = d.getFullYear();
	if (day < 10) {
		day = "0" + day;
	}
	if (month < 10) {
		month = "0" + month;
	}
	var date = year + "-" + month + "-" + day;

	return date;
};

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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