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
		<span style="font-size: 18px;">{{$title}}</span>
		<!-- <span class="text-purple"><small>{{$title_jp}}</small></span> -->
		<span class="pull-right" style="font-size: 16px;text-align: right;" id="monthTitle"></span>
	</h1>
	<ol class="breadcrumb">
	</ol>
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
				<!-- <div class="box-header">
					<h3 class="box-title">Serial Number Control Filters</h3>
				</div> -->
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 no-padding">
						<div class="col-md-3" style="padding-right: 5px;padding-left: 0px;">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" placeholder="Date From">
								</div>
							</div>
						</div>
						<div class="col-md-3" style="padding-right: 5px;padding-left: 5px;">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" name="dateto"  placeholder="Date To">
								</div>
							</div>
						</div>
						<div class="col-md-6" style="padding-right: 0px;padding-left: 5px;">
							<div class="form-group">
								<label>Material</label><br>
								<select class="form-control select2" style="width: 80%" data-placeholder="Select Material" id="material">
									<option value=""></option>
									@foreach($material as $material)
									<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
									@endforeach
								</select>
								<button id="search" onClick="fillData()" class="btn btn-primary" style="margin-left: 5px;">Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div id="container" style="width:100%; height:45vh;">
								
							</div>
						</div>
						<div class="col-md-12" style="overflow-x: scroll;" id="div_detail">
							<!-- <div style="background-color: orange;text-align: center;color: white;font-size: 20px;font-weight: bold;margin-bottom: 10px" id="titleFungsi">
								<span style="width: 100%">QA KENSA</span>
							</div> -->
							<table id="tableNgReport" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);" id="headTableNgReport">
								</thead>
								<tbody id="bodyTableNgReport">
								</tbody>
								<tfoot id="footTableNgReport">
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/highcharts.js') }}"></script>
<script src="{{ url('js/highcharts-3d.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#dateto').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function sortArray(array, property, direction) {
	    direction = direction || 1;
	    array.sort(function compare(a, b) {
	        let comparison = 0;
	        if (a[property] > b[property]) {
	            comparison = 1 * direction;
	        } else if (a[property] < b[property]) {
	            comparison = -1 * direction;
	        }
	        return comparison;
	    });
	    return array; // Chainable
	}

	function fillData(){
		$('#loading').show();
		$('#flo_detail_table').DataTable().destroy();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		url	= '{{ url("fetch/assembly/serial_number_control") }}';
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			material:$('#material').val(),
			origin_group_code:'{{$origin_group_code}}'
			// model:model
		}
		$.get(url,data, function(result, status, xhr){
			if(result.status){
				$('#headTableNgReport').html('');
				$('#footTableNgReport').html('');
				$('#bodyTableNgReport').html("");
				var tableHead = '';
				var tableFoot = '';

				tableHead += '<tr>';
					tableHead += '<th style="width: 1%">#</th>';
					tableHead += '<th style="width: 1%">Production Date</th>';
					tableHead += '<th style="width: 1%">Material Number</th>';
					tableHead += '<th style="width: 2%">Description</th>';
					tableHead += '<th style="width: 1%">Serial Number</th>';
					tableHead += '<th style="width: 1%">FLO Number</th>';
					tableHead += '<th style="width: 1%">Destination Code</th>';
					tableHead += '<th style="width: 1%">Destination Name</th>';
					tableHead += '<th style="width: 1%">Invoice</th>';
				tableHead += '</tr>';

				tableFoot += '<tr>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
					tableFoot += '<th></th>';
				tableFoot += '</tr>';

				$('#headTableNgReport').append(tableHead);
				$('#footTableNgReport').append(tableFoot);

				var tableData = "";
				
				var index = 1;
				$.each(result.report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+index+'</td>';
					tableData += '<td>'+value.date+' '+value.time+'</td>';
					tableData += '<td>'+value.material_number+'</td>';
					tableData += '<td>'+value.material_description+'</td>';
					tableData += '<td>'+value.serial_number+'</td>';
					tableData += '<td>'+value.flo_number+'</td>';
					tableData += '<td>'+value.destination_code+'</td>';
					tableData += '<td>'+value.destination_shortname+'</td>';
					tableData += '<td>'+(value.invoice_number || '')+'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#tableNgReport').DataTable().clear();
				$('#tableNgReport').DataTable().destroy();
				$('#bodyTableNgReport').append(tableData);

				$('#tableNgReport tfoot th').each( function () {
	                var title = $(this).text();
	                $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	            } );

	            var table = $('#tableNgReport').DataTable({
	            'dom': 'Bfrtip',
	                'responsive':true,
	                'lengthMenu': [
	                [ 15, 25, -1 ],
	                [ '15 rows', '25 rows', 'Show all' ]
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
	                "bJQueryUI": true,
	            "bAutoWidth": false,
	            "processing": true,
	            'ordering' :false,
	                initComplete: function() {
	                this.api()
	                    .columns([3, 6, 7])
	                    .every(function(dd) {
	                        var column = this;
	                        var theadname = $("#tableNgReport th").eq([dd]).text();
	                        var select = $(
	                                '<select style="color:black;"><option value="" style="font-size:11px;">All</option></select>'
	                            )
	                            .appendTo($(column.footer()).empty())
	                            .on('change', function() {
	                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

	                                column.search(val ? '^' + val + '$' : '', true, false)
	                                    .draw();
	                            });
	                        column
	                            .data()
	                            .unique()
	                            .sort()
	                            .each(function(d, j) {
	                                var vals = d;
	                                if ($("#tableNgReport th").eq([dd]).text() == 'Category') {
	                                    vals = d.split(' ')[0];
	                                }
	                                select.append('<option style="font-size:11px;" value="' +
	                                    d + '">' + vals + '</option>');
	                            });
	                    });
	                },
	            });

	            table.columns().every( function () {
	                var that = this;
	                $( '#search', this.footer() ).on( 'keyup change', function () {
	                    if ( that.search() !== this.value ) {
	                        that
	                        .search( this.value )
	                        .draw();
	                    }
	                } );
	            } );

	            $('#tableNgReport tfoot tr').appendTo('#tableNgReport thead');

				$('#monthTitle').html('Last Update : '+getActualFullDate()+'');

				var destination = [];
				for(var i = 0; i < result.report.length;i++){
					destination.push(result.report[i].destination_code);
				}

				var destination_unik = destination.filter(onlyUnique);

				var dests = [];
				for(var i = 0; i < destination_unik.length;i++){
					var qty = 0;
					var destination_shortname = '';
					for(var j = 0; j < result.report.length;j++){
						if (result.report[j].destination_code == destination_unik[i]) {
							destination_shortname = result.report[j].destination_shortname;
							qty++;
						}
					}
					dests.push({
						y:parseInt(qty),
						descode:destination_unik[i],
						desname:destination_shortname,
					});
				}

				var dests_sort = sortArray(dests, "y", -1);

				var category = [];
				var series = [];
				for(var i = 0; i < dests_sort.length;i++){
					category.push(dests_sort[i].descode+'<br>'+dests_sort[i].desname);
					series.push({y:parseInt(dests_sort[i].y),key:dests_sort[i].desname});
				}

                var chart = new Highcharts.Chart({
                    colors: ['rgba(119, 152, 191, 0.80)', 'rgba(144, 238, 126, 0.80)',
                        'rgba(247, 163, 92, 0.80)'
                    ],
                    chart: {
                        renderTo: 'container',
                        type: 'column',
                    },
                    title: {
                        text: 'Serial Number Resume By Destination'
                    },
                    subtitle: {
                        text: result.monthTitle,
                        style:{
                        	fontSize:'15px'
                        }
                    },
                    xAxis: {
                        categories: category,
                        gridLineWidth: 1,
                        scrollbar: {
                            enabled: true
                        }
                    },
                    legend:{
                    	enabled:false
                    },
                    yAxis: {
                        min: 1,
                        title: {
                            text: 'Quantity Set(s)'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                            },
                            formatter: function() {
                                return this.total + " Set(s)";
                            }
                        },
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderColor: '#303030',
                            // cursor: 'pointer',
                            stacking: 'normal',
                            point: {
                                events: {
                                    click: function() {
                                        // alert('Destinasi: ' + this.category + ', Location: ' + this.series.name +', qty: ' + this.y);
                                        // selectDest(this.category);
                                    }
                                }
                            }
                        },
                        column: {
                            minPointLength: 4
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': ' + this.y + '<br/>';
                        }
                    },
                    series: [{
                        name: 'Qty Set',
                        data: series,
                    }]
                });

				$('#loading').hide();

			}
			else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});
	}

	function showModal(category) {
		location.href='#div_detail';
		$('#select_category').val(category).trigger('change');
	}

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
		return  day + "-" + month + "-" + year+' ('+h + ":" + m + ":" + s+')';
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
</script>
@endsection