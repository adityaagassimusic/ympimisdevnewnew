@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  /*border: 1px solid #eeeeee !important;*/
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}
	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
	}

	 div#tableDetail_info.dataTables_info,
	 div#tableDetail_filter.dataTables_filter label,
	 div#tableDetail_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetail_info.dataTables_info,
	#tableDetail_info.dataTables_length {
		color: black;
	}

	div#tableDetailPie_info.dataTables_info,
	 div#tableDetailPie_filter.dataTables_filter label,
	 div#tableDetailPie_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailPie_info.dataTables_info,
	#tableDetailPie_info.dataTables_length {
		color: black;
	}

	div#tableDetailCategory_info.dataTables_info,
	 div#tableDetailCategory_filter.dataTables_filter label,
	 div#tableDetailCategory_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailCategory_info.dataTables_info,
	#tableDetailCategory_info.dataTables_length {
		color: black;
	}
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 27%;">
      <span style="font-size: 40px">Loading, please wait a moment . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;padding-right: 5px">
				<div id="container1" style="width: 100%;height: 500px;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px; padding-right: 5px">
				<table class="table table-hover table-bordered" id="tableListMonitoring">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
									<th style="width: 5%">No</th>
									<th style="width: 10%">Request Id</th>
									<th style="width: 15%">Departemen</th>
									<th style="width: 10%">Pembuat</th>
									<th style="width: 5%">Tanggal Pengajuan</th>
									<th style="width: 30%">Progress Approval</th>
									<th style="width: 10%">Status</th>
								</tr>
						</thead>
						<tbody id="tableBodyListMonitoring">
						</tbody>
					</table>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px;color: black" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 5%;">#</th>
								<th style="width: 15%;">Employee ID</th>
								<th style="width: 20%;">Name</th>
								<th style="width: 15%;">Dept</th>
								<th style="width: 15%;">Section</th>
								<th style="width: 10%;">Group</th>
								<th style="width: 10%;">Sub Group</th>
								<th style="width: 10%;">Proces</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var intervalChart;

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		ListMonitoring();
		// intervalChart = setInterval(fillChart, 60000);
		// console.log(result.survey);

		$('#old_nik').show();
		$('#name').show();
		$('#address').show();
		$('#no_whatsapp').show();
		$('#department').show();
		$('#section').show();
		$('#group').show();
		$('#sub_group').show();
		$('#end_date').show();
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

	function fillChart() {
    $("#loading").show();

		var tanggal = $('#tanggal').val();
		
		var data = {
			tanggal:tanggal
		}

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		$.get('{{ url("fetch/monitoring/request") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
		      	$("#loading").hide();
					//Chart Machine Report
					var dpt = [];
					var bersedia = [];
					var keterangan = 'Request Manpower';

					// for (var i = 0; i < result.survey.length; i++) {
					// 	dept.push(result.survey[i].dpt);
					// 	bersedia = bersedia+parseInt(result.survey[i].bersedia);
					// }
					$.each(result.survey, function(key, value) {
			            dpt.push(value.dpt);
			            // not_sign.push(parseInt(value.NotSigned));
			            bersedia.push(parseInt(value.bersedia));
			          });

					Highcharts.chart('container1', {
						chart: {
							type: 'column',
							backgroundColor : '#212121'
						},
						title: {
							text: 'Resume '+keterangan,
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: dpt,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total',
								style: {
			                        color: '#eee',
			                        fontSize: '15px',
			                        fontWeight: 'bold',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"15px"
								}
					        },
							type: 'linear',
							opposite: true
						},
					    ],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							// layout: 'horizontal',
							// backgroundColor:
							// Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							// itemStyle: {
				   //              fontSize:'12px',
				   //          },
				   enabled: false
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                      ShowModal(this.category,this.series.name);
				                    }
				                  }
				                },
				                animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							type: 'column',
							data: bersedia,
							name: 'Bersedia',
							colorByPoint: false,
							color: "#32a852"
						}
						]
					});
				}
			}
		});

	}

	function ListMonitoring(){
		$.get('{{ url("fetch/monitoring/request") }}', function(result, status, xhr){
			if(result.status){
				$('#tableListMonitoring').DataTable().clear();
				$('#tableListMonitoring').DataTable().destroy();
				var tableData = '';
				$('#tableBodyListMonitoring').html("");
				$('#tableBodyListMonitoring').empty();
		        var count = 1;
		        $.each(result.list, function(key, value) {
		        	var  appr = value.approval.split(',');
		        	var  status = value.status.split(',');

		        	tableData += '<tr onclick="DetailApproval(\''+value.request_id+'\')" style="border:1px solid black !important;">';
		        	tableData += '<td>'+ count +'</td>';
		        	tableData += '<td>'+ value.request_id +'</td>';
		        	tableData += '<td>'+ value.department +'</td>';
		        	tableData += '<td>'+ value.name +'</td>';
		        	tableData += '<td>'+ value.created_at +'</td>';
		        	tableData += '<td>';
		        	for(var i = 0; i < appr.length; i++){
		        		if (i == appr.length-1){
		        			if (status[i] == 'Approved') {
		        				tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+appr[i]+'</span>';  
		        			}
		        			else if(status[i] == 'none'){
		        				tableData += '';  
		        			}
		        			else{
		        				tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+appr[i]+'</span>'; 
		        			}
		        		}else{
		        			if (status[i] == 'Approved') {
		        				tableData += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+appr[i]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; ';  
		        			}
		        			else if(status[i] == 'none'){
		        				tableData += '';  
		        			}
		        			else{
		        				tableData += '<span class="label" style="color: black; background-color: #e74c3c; border: 1px solid black;">'+appr[i]+'</span> &nbsp;<i class="fa fa-caret-right"></i>&nbsp; '; 
		        			}
		        		}
		        	}
		        	tableData += '</td>';
		        	tableData += '<td>'+ value.remark +'</td>';
		        	tableData += '</tr>';
		        	count += 1;
		        });
		        $('#tableBodyListMonitoring').append(tableData);
      }
      else{
      	openErrorGritter('Error!', result.message);
      }
    });
}

	function ShowModal(department,answer) {
		clearInterval(intervalChart);
		$('#modalDetail').modal('show');
		$('#loadingDetail').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();


    $("#loading").show();

		var tanggal = $('#tanggal').val();

		var data = {
			department:department,
			answer:answer,
			tanggal:tanggal
		}

		$.get('{{ url("fetch/veteran/employee/detail") }}', data, function(result, status, xhr) {
			if(result.status){

      	$("#loading").hide();
				$('#tableDetailBody').html('');

				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				var keterangan = "Veteran Employee";

				$.each(result.survey, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.old_nik +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '<td>'+ value.section +'</td>';
					resultData += '<td>'+ value.group +'</td>';
					resultData += '<td>'+ value.sub_group +'</td>';
					resultData += '<td>'+ value.proces +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Veteran Employee '"+answer+"'</span></center>");

				$('#loadingDetail').hide();
				$('#tableDetail').show();
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				intervalChart = setInterval(fillChart,60000);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
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

	function ClearAll(){
		$('#old_nik').val('').trigger('change');
		$('#name').val('');
		$('#address').val('');
		$('#no_whatsapp').val('');
		$('#department').val('');
		$('#section').val('');
		$('#group').val('');
		$('#sub_group').val('');
		$('#proces').val('');
		$('#end_date').val('');
		$('#remark').val('');
	}

</script>
@endsection