@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	tbody>tr>td{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	tfoot>tr>th{
		/*text-align:center;*/
		vertical-align: middle;
		padding: 2px;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(173, 173, 173);
		padding: 0;
		vertical-align: middle;
		padding: 2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(173, 173, 173);
		vertical-align: middle;
		padding: 2px;
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
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Serial Number</th>
								<th style="width: 2%;">Model</th>
								<th style="width: 3%;">Loc</th>
								<th style="width: 3%;">NG Name</th>
								<th style="width: 3%;">Onko</th>
								<th style="width: 3%;">Qty</th>
								<th style="width: 3%;">Value Atas</th>
								<th style="width: 3%;">Value Bawan</th>
								<th style="width: 3%;">NG Loc</th>
								<th style="width: 3%;">Emp Kensa</th>
								<th style="width: 3%;">At</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="6">TOTAL</th>
								<th colspan="6" style="text-align: left;" id="total_all"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCouncelingInjeksi">
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
								<th style="width: 1%;">Status</th>
								<th style="width: 1%;">ID</th>
								<th style="width: 2%;">Name</th>
								<th style="width: 2%;">Tag</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="bodyTableCouncelingInjeksi">
						</tbody>
					</table>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px;">
		              	<label for="">Document Training</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px;">
					  	<a href="{{url('input/injection/training_document/qa')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
					  </div>
		            </div>
		            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
		            	<span style="padding: 20px;font-size: 20px">CREATE CAR</span>
		            </div>
		            <div class="col-xs-12" style="padding-top: 20px" id="div_reject_reason_inj">
		            	<div class="row">
		            		<div class="form-group">
								<label style="color: red">Reject Reason</label><br>
								<textarea id="reject_reason_inj" style="width: 100%" readonly></textarea>
							</div>
		            	</div>
		            </div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="row">
							<div class="form-group">
								<label>Deskripsi Injection</label><br>
								<textarea id="car_description_inj" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Immediately Action Injection</label><br>
								<textarea id="car_action_now_inj" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Possibility Cause Injection</label><br>
								<textarea id="car_cause_inj" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Corrective Action Injection</label><br>
								<textarea id="car_action_inj" style="width: 100%"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel('injeksi')">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalCouncelingAssy">
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
								<th style="width: 1%;">Status</th>
								<th style="width: 1%;">ID</th>
								<th style="width: 2%;">Name</th>
								<th style="width: 2%;">Tag</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="bodyTableCouncelingAssy">
						</tbody>
					</table>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px;">
		              	<label for="">Document Training</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px;">
					  	<a href="{{url('input/recorder/training_document')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
					  </div>
		            </div>
		            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
		            	<span style="padding: 20px;font-size: 20px">CREATE CAR</span>
		            </div>
		            <div class="col-xs-12" style="padding-top: 20px" id="div_reject_reason_rc">
		            	<div class="row">
		            		<div class="form-group">
								<label style="color: red">Reject Reason</label><br>
								<textarea id="reject_reason_rc" style="width: 100%" readonly></textarea>
							</div>
		            	</div>
		            </div>
		            <div class="col-xs-12" style="padding-top: 20px">
						<div class="row">
							<div class="form-group">
								<label>Deskripsi Assy</label><br>
								<textarea id="car_description_rc" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Immediately Action Assy</label><br>
								<textarea id="car_action_now_rc" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Possibility Cause Assy</label><br>
								<textarea id="car_cause_rc" style="width: 100%"></textarea>
							</div>
							<div class="form-group">
								<label>Corrective Action Assy</label><br>
								<textarea id="car_action_rc" style="width: 100%"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel('assy')">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

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

	$.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

	jQuery(document).ready(function(){
		$('#div_reject_reason_inj').hide();
		$('#div_reject_reason_rc').hide();
		CKEDITOR.replace('car_description_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('car_description_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('reject_reason_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('reject_reason_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
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
		setInterval(fetchChart, 20000);
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

	var reject_reason_inj = '';
	var reject_reason_rc = '';

	function fetchChart(){

		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/recorder/display/qa_audit") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
				$('#container1').html('');
				var container = '';
				container += '<table class="table table-responsive" id="tableAudit" style="border:2px solid white;margin-bottom:0px" width="200px">';
				container += '<tr style="background-color:#159925">';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Date</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Auditor QA</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Kakunin RC</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Injeksi</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Product</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Defect</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px;width:100px">Area</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Category</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Image</th>';
				container += '<th style="border:2px solid white;padding:0px;color:white;text-align:center;font-size:40px">Training</th>';
				container += '</tr>';
				var index = 1;
				for(var i = 0; i < result.audit.length; i++){
					if (index %2 == 0) {
						var color = '#0072ab';
					}else{
						var color = '#124182';
					}
					// container += '<div style="width:200px">';
					var urlauditor = '{{ url("images/avatar/") }}/'+result.audit[i].auditor.split(' - ')[0]+'.jpg';
					var urlauditee = '{{ url("images/avatar/") }}/'+result.audit[i].auditee.split(' - ')[0]+'.jpg';
					if (result.audit[i].pic_injection == null) {
						urlinjection = '';
					}else{
						var urlinjection = '{{ url("images/avatar/") }}/'+result.audit[i].pic_injection.split(' - ')[0]+'.jpg';
					}
					var urlimage = 'http://10.109.52.4/mirai/public/data_file/recorder/qa_audit/'+result.audit[i].image;
					container += '<tr style="background-color:'+color+'">';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:50px">'+result.audit[i].check_date+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:200px" src="'+urlauditor+'" class="user-image" alt="User image"><br><span style="font-size:35px">'+result.audit[i].auditor.split(' - ')[0]+'<br>'+result.audit[i].auditor.split(' - ')[1].split(' ').slice(0,2).join(' ')+'</span></td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:200px" src="'+urlauditee+'" class="user-image" alt="User image"><br><span style="font-size:35px">'+result.audit[i].auditee.split(' - ')[0]+'<br>'+result.audit[i].auditee.split(' - ')[1].split(' ').slice(0,2).join(' ')+'</span></td>';
					if (result.audit[i].pic_injection != null) {
						container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:200px" src="'+urlinjection+'" class="user-image" alt="User image"><br><span style="font-size:35px">'+result.audit[i].pic_injection.split(' - ')[0]+'<br>'+result.audit[i].pic_injection.split(' - ')[1].split(' ').slice(0,2).join(' ')+'</span></td>';
					}else{
						container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:200px" src="'+urlinjection+'" class="user-image" alt="User image"></td>';
					}
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:50px;padding-right:0px;padding-left:0px;">'+result.audit[i].part_name+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:50px;">'+result.audit[i].defect+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:50px;">'+result.audit[i].area+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle;font-size:50px;">'+result.audit[i].category+'</td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle"><img style="width:150px" src="'+urlimage+'" class="user-image" alt="User image"></td>';
					container += '<td style="border:2px solid white;padding:0px;color:white;vertical-align:middle">';
					if (result.audit[i].counceled_employee == null) {
						container += '<button class="btn btn-warning btn-lg"  style="font-weight:bold;font-size:20px;margin-bottom:20px;" onclick="councelingModalInjeksi(\''+result.audit[i].auditee.split(' - ')[0]+'\',\''+result.audit[i].auditee.split(' - ')[1]+'\',\''+result.audit[i].pic_injection.split(' - ')[0]+'\',\''+result.audit[i].pic_injection.split(' - ')[1]+'\',\''+result.audit[i].id_audit+'\')">TRAINING <br>& KONSELING <br>INJEKSI</button><br>';
					}else{
						var url = '{{url("print/recorder/qa_audit")}}/injeksi/'+result.audit[i].id_audit;
						container += '<a target="_blank" class="btn btn-success btn-lg" href="'+url+'" style="font-weight:bold;font-size:20px">HASIL TRAINING <br>& KONSELING <br>INJEKSI</a><br>';
					}
					if (result.audit[i].counceled_by == null) {
						container += '<button class="btn btn-primary btn-lg"  style="font-weight:bold;font-size:20px" onclick="councelingModalAssy(\''+result.audit[i].auditee.split(' - ')[0]+'\',\''+result.audit[i].auditee.split(' - ')[1]+'\',\''+result.audit[i].pic_injection.split(' - ')[0]+'\',\''+result.audit[i].pic_injection.split(' - ')[1]+'\',\''+result.audit[i].id_audit+'\')">TRAINING <br>& KONSELING <br>ASSY</button>';
					}else{
						var url = '{{url("print/recorder/qa_audit")}}/assy/'+result.audit[i].id_audit;
						container += '<a target="_blank" class="btn btn-success btn-lg" href="'+url+'" style="font-weight:bold;font-size:20px">HASIL TRAINING <br>& KONSELING <br>ASSY</a><br>';
					}
					if (result.audit[i].reject_reason_inj != null) {
						reject_reason_inj = result.audit[i].reject_reason_inj;
					}
					if (result.audit[i].reject_reason_rc != null) {
						reject_reason_rc = result.audit[i].reject_reason_rc;
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

function submitCouncel(param) {
	$('#loading').show();
	if (param === 'injeksi') {
		if ($('#tag_injeksi').val() == "" 
			|| CKEDITOR.instances.car_description_inj.getData() == ""
			|| CKEDITOR.instances.car_action_now_inj.getData() == ""
			|| CKEDITOR.instances.car_cause_inj.getData() == ""
			|| CKEDITOR.instances.car_action_inj.getData() == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Semua Data Harus Diisi');
			return false;
		}
		var counceled_employee = $("#tag_injeksi").val();
		var id_audit = $("#id_audit_injeksi").val();

		var description_inj = CKEDITOR.instances.car_description_inj.getData();
		var action_now_inj = CKEDITOR.instances.car_action_now_inj.getData();
		var cause_inj = CKEDITOR.instances.car_cause_inj.getData();
		var action_inj = CKEDITOR.instances.car_action_inj.getData();

		var formData = new FormData();
		formData.append('counceled_employee', counceled_employee);
		formData.append('id_audit', id_audit);
		formData.append('description_inj',description_inj);
		formData.append('action_now_inj',action_now_inj);
		formData.append('cause_inj',cause_inj);
		formData.append('action_inj',action_inj);
		formData.append('param',param);
	}else{
		if ($('#tag_auditee').val() == "" || CKEDITOR.instances.car_description_rc.getData() == ""
			|| CKEDITOR.instances.car_action_now_rc.getData() == ""
			|| CKEDITOR.instances.car_cause_rc.getData() == ""
			|| CKEDITOR.instances.car_action_rc.getData() == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Semua Data Harus Diisi');
			return false;
		}
		var counceled_by = $("#tag_auditee").val();
		var id_audit = $("#id_audit_assy").val();

		var description_rc = CKEDITOR.instances.car_description_rc.getData();
		var action_now_rc = CKEDITOR.instances.car_action_now_rc.getData();
		var cause_rc = CKEDITOR.instances.car_cause_rc.getData();
		var action_rc = CKEDITOR.instances.car_action_rc.getData();

		var formData = new FormData();
		formData.append('counceled_by', counceled_by);
		formData.append('id_audit', id_audit);
		formData.append('description_rc',description_rc);
		formData.append('action_now_rc',action_now_rc);
		formData.append('cause_rc',cause_rc);
		formData.append('action_rc',action_rc);
		formData.append('param',param);
	}

	$.ajax({		
		url:"{{ url('input/recorder/counceling') }}",
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
			$('#modalCouncelingInjeksi').modal('hide');
			$('#modalCouncelingAssy').modal('hide');
			openSuccessGritter('Success','Input Konseling Berhasil');
		},
		error: function (err) {
	        openErrorGritter('Error!',err);
	    }
	})
}

function councelingModalInjeksi(auditee_id,auditee_name,injeksi_id,injeksi_name,id_audit) {
	$('#modalCouncelingInjeksi').modal('show');


	$('#bodyTableCouncelingInjeksi').html('');

	var bodyCounceling = '';
	var injeksi = 'tag_injeksi';

	bodyCounceling += '<tr>';
	bodyCounceling += '<td>PIC Injeksi</td>';
	bodyCounceling += '<td>'+injeksi_id+'</td>';
	bodyCounceling += '<td>'+injeksi_name+' <input type="hidden" value="'+id_audit+'" id="id_audit_injeksi"></td>';
	bodyCounceling += '<td><input class="form-control" onkeyup="scanIdCard(this.id,event)" id="tag_injeksi" style="width:100%" placeholder="Scan ID Card PIC Injeksi"></td>';
	bodyCounceling += '<td style="text-align:center"><button class="btn btn-danger" onclick="cancelScan(\''+injeksi+'\')">Cancel</button></td>';
	bodyCounceling += '</tr>';

	$('#bodyTableCouncelingInjeksi').append(bodyCounceling);

	$('#tag_injeksi').val('');

	$('#tag_injeksi').focus();

	$('#div_reject_reason_inj').hide();
	if (reject_reason_inj != '') {
		$("#reject_reason_inj").html(CKEDITOR.instances.reject_reason_inj.setData(reject_reason_inj));
		$('#div_reject_reason_inj').show();
	}
}

function councelingModalAssy(auditee_id,auditee_name,injeksi_id,injeksi_name,id_audit) {
	$('#modalCouncelingAssy').modal('show');


	$('#bodyTableCouncelingAssy').html('');

	var bodyCounceling = '';
	var auditee = 'tag_auditee';

	bodyCounceling += '<tr>';
	bodyCounceling += '<td>Auditee</td>';
	bodyCounceling += '<td>'+auditee_id+'</td>';
	bodyCounceling += '<td>'+auditee_name+' <input type="hidden" value="'+id_audit+'" id="id_audit_assy"></td>';
	bodyCounceling += '<td><input class="form-control" onkeyup="scanIdCard(this.id,event)" id="tag_auditee" style="width:100%" placeholder="Scan ID Card Auditee"></td>';
	bodyCounceling += '<td style="text-align:center"><button class="btn btn-danger" onclick="cancelScan(\''+auditee+'\')">Cancel</button></td>';
	bodyCounceling += '</tr>';

	$('#bodyTableCouncelingAssy').append(bodyCounceling);

	$('#tag_auditee').val('');

	$('#tag_auditee').focus();

	$('#div_reject_reason_rc').hide();
	if (reject_reason_rc != '') {
		$("#reject_reason_rc").html(CKEDITOR.instances.reject_reason_rc.setData(reject_reason_rc));
		$('#div_reject_reason_rc').show();
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
				// "footerCallback": function ( row, data, start, end, display ) {
		  //           var api = this.api(), data;
		 
		  //           var intVal = function ( i ) {
		  //               return typeof i === 'string' ?
		  //                   i.replace(/[\$,]/g, '')*1 :
		  //                   typeof i === 'number' ?
		  //                       i : 0;
		  //           };

		  //           pageTotal = api
		  //               .column( 7, { page: 'current'} )
		  //               .data()
		  //               .reduce( function (a, b) {
		  //                   return intVal(a) + intVal(b);
		  //               }, 0 );
		 
		  //           $( api.column( 7 ).footer() ).html(
		  //               pageTotal
		  //           );
		  //       }
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