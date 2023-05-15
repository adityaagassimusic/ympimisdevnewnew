@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#listTableBodyOutstanding > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
	}
	.container {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.container input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.container .checkmark:after {
	  left: 10px;
	  top: 5px;
	  width: 5px;
	  height: 12px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Tools Usage and History Data <span class="text-purple"></span>
	</h1>
	<ol class="breadcrumb">
		<!-- <li><a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;{{ $title }}</a></li> -->
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #a1887f ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;color:white" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">Tools Usage and History Data</span>
							<span style="font-size: 25px;color: black;width: 25%;"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
	    	<div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
	    		<div id="chart2" style="width: 100%"></div>
	    	</div>
	    </div>
	</div>
	<div class="box">
		<div class="box-body">
			<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
	    	<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Tanggal</th>
						<th>Employee ID</th>
						<th>Nama</th>
						<th>Rack Code</th>
						<th>Tools</th>
						<th>Remark</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

</section>


@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


    var no = 0;

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
		drawChart();
	});

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function getFormattedDate(date) {
	  var year = date.getFullYear();

	  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
		];

	  var month = date.getMonth();

	  var day = date.getDate().toString();
	  day = day.length > 1 ? day : '0' + day;
	  
	  return day + '-' + monthNames[month] + '-' + year;
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/tools/log") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.tools, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.tanggal))+'</td>';
					listTableBody += '<td style="width:1%;">'+value.employee_id+'</td>';
					listTableBody += '<td style="width:2%;">'+value.employee_name+'</td>';
					listTableBody += '<td style="width:2%;">'+value.rack_code+'</td>';
					listTableBody += '<td style="width:3%;">'+value.item_code+' - '+value.description+'</td>';
					listTableBody += '<td style="width:2%;">'+value.kategori+'</td>';
					listTableBody += '<td style="width:1%;">'+value.qty+'</td>';
					listTableBody += '</tr>';

					count_all += 1;
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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
					'pageLength': 20,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	function drawChart() {
		$.get('{{ url("fetch/tools/log/monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var bulan = [], jumlah = [];

					$.each(result.datas, function(key, value) {
						bulan.push(value.bulan);
						jumlah.push(parseInt(value.jumlah));
					});

					var date = new Date();

					$('#chart2').highcharts({
						chart: {
							type: 'column',
							height : '250px'
						},
						title: {
							text: ''
						},
						credits : {
							enabled:false
						},
						xAxis: {
							type: 'category',
							categories: bulan
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total Pengambilan Tools'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
	                        color: ( 
	                        	Highcharts.defaultOptions.title.style &&
	                        	Highcharts.defaultOptions.title.style.color
	                        	) || 'gray'
	                      }
	                    },
             			tickInterval: 10
	                  },
	                  legend: {
	                  	align: 'right',
	                  	x: -30,
	                  	verticalAlign: 'top',
	                  	y: 25,
	                  	floating: true,
	                  	backgroundColor:
	                  	Highcharts.defaultOptions.legend.backgroundColor || 'white',
	                  	borderColor: '#CCC',
	                  	borderWidth: 1,
	                  	shadow: false,
	                  	enabled:false
	                  },
	                  tooltip: {
	                  	headerFormat: '<b>{point.x}</b><br/>',
	                  	pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
	                  },
	                  plotOptions: {
	                  	column: {
	                  		stacking: 'normal',
	                  		dataLabels: {
	                  			enabled: true
	                  		}
	                  	}
	                  },
	                  series: [{
	                  	name: 'Jumlah',
	                  	data: jumlah,
	                  	color: '#ffab40'
	                  }]
	                })
				} else{
					alert('Attempt to retrieve data failed');
				}
			}
		})
	}

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

