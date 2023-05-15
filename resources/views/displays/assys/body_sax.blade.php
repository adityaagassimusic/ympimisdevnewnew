@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

<style type="text/css">
	table.table-bordered{
		border:1px solid black;
		/*background-color: white;*/
		color:white;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 1px solid black;
		/*font-size: 1vw;*/
		font-weight: bold;
	}
	.table > tbody > tr > th {
		padding: 2px;
		text-align: center;
		color: black;
		background-color: white;
	}
	#assyTable > tbody > tr > td {
		text-align: right;
	}
	#detailTabel {
		color: black;
	}
	.stock {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #a9ff96;
		display: inline-block;
	}
	.lcq {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #fcf33a;
		display: inline-block;
	}
	.brl {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #722973;
		display: inline-block;
	}
	.wld {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #7cb5ec;
		display: inline-block;
	}
	.bff {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #e096ff;
		display: inline-block;
	}
	.plt {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: silver;
		display: inline-block;
	}
	.acc {
		background: #c8cfcb;
		color: black;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="height:100%">
			<p style="color: white; font-weight: bold">Last Update at <span id="update_at"></span></p>
		</div>
		{{-- <div class="col-xs-12">
			<div id="picking_chart" style="width: 100%; margin: auto"></div>
		</div> --}}
		<div class="col-xs-12">
			<center><div id="judul" style="color:white; font-weight: bold; font-size: 2vw"></div></center>
		</div>

		<div class="col-xs-12" style="margin-top: 10px">
			<form method="GET" action="{{ url('index/display/picking/body/'.$option) }}">
				<div class="col-xs-2" style="line-height: 1">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" name="date" placeholder="Select Date" style="border-color: #00a65a" <?php if (isset($_GET['date'])): ?>
						<?php echo "value=".$_GET['date']; endif ?>>
					</div>
					<br>
				</div>
				<div class="col-xs-2">
					<select class="form-control select2" multiple="multiple" id="key" onchange="change()" data-placeholder="Select Key">
						@foreach($keys as $key)
						<option value="{{ $key->key }}">{{ $key->key }}</option>
						@endforeach
					</select>
					<input type="text" name="key2" id="dd" hidden>
				</div>
				<div class="col-xs-1">
					<select class="form-control select2" multiple="multiple" id="modelselect" onchange="changeModel()" data-placeholder="Select Model">
						@foreach($models as $model)
						<option value="{{ $model->model }}">{{ $model->model }}</option>
						@endforeach
					</select>
					<input type="text" name="model2" id="model2" hidden>
				</div>

				<!-- JIKA SUB ASSY -->

				<div class="col-xs-2">
					<select class="form-control select2" id="surface" multiple="multiple" onchange="changeSurface()" data-placeholder="Select Surface">
						@foreach($surfaces as $surface)
						<option value="{{ $surface[0] }}">{{ $surface[1] }}</option>
						@endforeach
					</select>
				</div>
				<input type="text" name="surface2" id="surface2" hidden>

				<div class="col-xs-1">
					<select class="form-control select2" id="hpl" multiple="multiple" onchange="changeHpl()" data-placeholder="Select HPL">
						@foreach($hpls as $hpl)
						<option value="{{ $hpl }}">{{ $hpl }}</option>
						@endforeach
					</select>
					<input type="text" name="hpl2" id="hpl2" hidden>
				</div>

				<div class="col-xs-2">
					<select class="form-control select2" id="order" onchange="changeOrder()" placeholder="Order by">
						<option value="">Diff</option>
						<option value="1" <?php if($_GET['order2'] == '1' ) echo "selected"; ?> >Stock Room</option>
						<option value="2" <?php if($_GET['order2'] == '2' ) echo "selected"; ?> >Availibility</option>
					</select>
					<input type="text" name="order2" id="order2" hidden>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" type="submit">Cari</button>
				</div>
			</form>
		</div>

		<div class="col-xs-12">
			<div id="main_grafik" style="height: 550px"></div>
		</div>

		<div class="modal fade" id="myModal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 style="float: right;" id="modal-title"></h4> 
						<h4 class="modal-title"><b id="titel"></b></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detailTabel">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th>Tag</th>
											<th>GMC</th>
											<th>Description</th>
											<th>Quantity</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th colspan="3" style="text-align:right">Total : </th>
											<th></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
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
		fill_table();

		var kunci = "{{$_GET['key2']}}";
		var kuncies = kunci.split(",");
		var kunciFilter = [];
		ctg = "";

		for(var i = 0; i < kuncies.length; i++){
			ctg = kuncies[i];

			if(kunciFilter.indexOf(ctg) === -1){
				kunciFilter[kunciFilter.length] = ctg;
			}
		}
		// alert(kunciFilter);

		$("#judul").text(kunciFilter+"-{{$_GET['model2']}}-{{$_GET['surface2']}}-{{$_GET['hpl2']}}");


		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

	function change() {
		$("#dd").val($("#key").val());
	}

	function changeModel() {
		$("#model2").val($("#modelselect").val());
	}
	function changeSurface() {
		$("#surface2").val($("#surface").val());
	}
	function changeHpl() {
		$("#hpl2").val($("#hpl").val());
	}
	function changeOrder() {
		$("#order2").val($("#order").val());
	}


	function fill_table() {
		var data = {
			tanggal:"{{$_GET['date']}}",
			key:"{{$_GET['key2']}}",
			model:"{{$_GET['model2']}}",
			surface:"{{$_GET['surface2']}}",
			hpl:"{{$_GET['hpl2']}}",
			order:"{{$_GET['order2']}}"
		}
		diffs = [];
		plans = [];

		$.get('{{ url("fetch/display/body/".$option) }}', data, function(result, status, xhr){
			if(result.status){
				$("#update_at").text("("+result.update_at+")");

				$("#model").empty();
				$("#plan").empty();
				$("#picking").empty();
				$("#diff").empty();

				$("#plan_acc").empty();
				$("#picking_acc").empty();
				$("#return_acc").empty();

				$("#stok").empty();
				$("#stok_all").empty();

				model = "<th style='width:45px'>#</th>";
				totplan = "<th>Plan</th>";
				picking = "<th>Pick</th>";

				diff = "<th>Diff</th>";
				

				planAcc = "<th>Plan acc</th>";
				pickAcc = "<th>Pick acc</th>";
				retunAcc = "<th>Return acc</th>";

				
				stk = "<th style='border: 1px solid white;'>Stock Room</th>";
				
				stk_als = "<th style='border: 1px solid white;' rowspan='2'>Availibility</th>";

				var style = "";

				temporary = [];

				for (var i = 0; i < result.plan.length; i++) {
					var z = [];
					z["model"] = result.plan[i].model;
					z["key"] = result.plan[i].key;

					if(typeof result.plan[i].surface === 'undefined') 
						z["surface"] = ""; 
					else 
						z["surface"] = result.plan[i].surface;

					z["plan"] = result.plan[i].plan;
					z["picking"] = result.plan[i].picking;
					z["plus"] = result.plan[i].plus;
					z["minus"] = result.plan[i].minus;
					z["stock"] = result.plan[i].stock;
					z["plan_ori"] = result.plan[i].plan_ori;
					z["diff"] = result.plan[i].diff;
					z["diff2"] = result.plan[i].diff2;

					z["ava"] = result.plan[i].ava;
					
					z["stockroom"] = result.stok[i].stockroom;
					z["barrel"] = result.stok[i].barrel;
					z["lacquering"] = result.stok[i].lacquering;
					z["plating"] = result.stok[i].plating;
					z["welding"] = result.stok[i].welding;
					z["buffing"] = result.stok[i].buffing;

					temporary.push(z);

				}

				// console.table(temporary);

				// console.table(result.plan);

				// console.table(result.stok);
					// console.log(result.plan.length+" "+result.stok.length);


					if ("{{$_GET['order2']}}" == "1") {
						temporary.sort(function(a, b) {
							return a['diff2'] - b['diff2'];
						});
					} else if ("{{$_GET['order2']}}" == "2") {
						temporary.sort(function(a, b) {
							return a['ava'] - b['ava'];
						});
					}

					var chart = [];
					var max_tmp = [];

					var plan_series = [];
					var pick_series = [];
					var category = [];

					$.each(temporary, function(index, value){
						var minus = 0;

						if (~value.key.indexOf("BODY") && (value.plan > 0 || value.picking > 0)) {
							chart.push([parseInt(value.plan), parseInt(value.picking),  value.model+' '+value.key+' '+value.surface]);
						}
					});

function comparator(a, b) {    
  if (a[0] > b[0]) return -1
  if (a[0] < b[0]) return 1
  return 0
}

chart = chart.sort(comparator)

					$.each(chart, function(index, value){
						plan_series.push(chart[index][0]);
						pick_series.push(parseInt(chart[index][1]));
						category.push(chart[index][2]);
					})


							Highcharts.chart('main_grafik', {
    chart: {
        type: 'column',
        backgroundColor: '#3c3c3c',
    },
    title: {
        text: ' '
    },
    xAxis: {
        categories: category
    },
    yAxis: [{
        min: 0,
        title: {
            text: 'Qty Item'
        }
    }],
    legend: {
        shadow: false
    },
    tooltip: {
        shared: true
    },
    credits:{
          enabled:false
        },
    plotOptions: {
        column: {
            grouping: false,
            shadow: false,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Plan',
        color: 'rgba(165,170,217,1)',
        data: plan_series,
        pointPadding: 0.1,
        // pointPlacement: -0.2
    }, {
        name: 'Picking',
        color: 'rgba(126,86,134,.9)',
        data: pick_series,
        pointPadding: 0.2,
        // pointPlacement: -0.2
    }]
});

					

					

			} else {
				openErrorGritter('Error', result.message);
			}
		// 	diffs = [];
	})

}

function openModal(kunci, lokasi) {
	$("#myModal").modal("show");
	$("#titel").text(kunci+" ("+lokasi+")");

	$('#detailTabel').DataTable().destroy();

	var data = {
		model:kunci.split(" ")[0],
		key:kunci.split(" ")[1],
		surface:kunci.split(" ")[2],
		location:lokasi
	}

	var table = $('#detailTabel').DataTable({
		'dom': 'Bfrtip',
		'responsive': true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'order': [],
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": false,
		"bAutoWidth": false,
		"processing": true,
		"serverSide": false,
		"ajax": {
			"type" : "get",
			"url" : "{{ url("fetch/detail/sub_assy") }}",
			"data" : data
		},
		"columns": [
		{ "data": "tag", "width" : "10%" },
		{ "data": "material_number", "width" : "10%" },
		{ "data": "material_description", "width" : "70%" },
		{ "data": "quantity", "width" : "10%", "className": "text-right"}
		],
		"footerCallback": function ( row, data, start, end, display ) {
			var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
            	return typeof i === 'string' ?
            	i.replace(/[\$,]/g, '')*1 :
            	typeof i === 'number' ?
            	i : 0;
            };

            // Total over all pages
            total = api
            .column( 3 )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Total over this page
            pageTotal = api
            .column( 3, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Update footer
            $( api.column( 3 ).footer() ).html(
            	total
            	);
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
		time: '4000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '4000'
	});
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
</script>
@endsection