@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
	tbody>tr>td{
		padding: 10px 5px 10px 5px;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 40px;
		padding:  2px 5px 2px 5px;
	}
	.control-label {
		padding-top: 0 !important;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalCreate()"><i class="fa fa-pencil-square-o"></i> Create New</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="xol-xs-12 col-md-12 col-lg-4" id="container1" style="height: 40vh;">
		</div>
		<div class="xol-xs-12 col-md-12 col-lg-2" style="height: 40vh;">
			<table id="tableResume" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="text-align: center; font-size: 20px;">Direct Supplier</th>
					</tr>
				</thead>
				<tbody>
					<tr style="height: 14vh;">
						<td style="text-align: center; background-color: #90ee7e; font-weight: bold; font-size: 90px;" id="count_active"></td>
					</tr>
				</tbody>
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="text-align: center; font-size: 20px;">Indirect Supplier</th>
					</tr>
				</thead>
				<tbody>
					<tr style="height: 14vh;">
						<td style="text-align: center; background-color: #555555; color: white; font-weight: bold; font-size: 90px;" id="count_inactive"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="xol-xs-12 col-md-12 col-lg-12">
			<table id="tableDetail" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 0.1%; text-align: center;">#</th>
						<th style="width: 3%;">Name</th>
						<th style="width: 0.1%; text-align: center;">Vendor</th>
						<th style="width: 0.1%;">Location</th>
						<th style="width: 0.1%;">Category</th>
						<th style="width: 1%;">Group</th>
						<th style="width: 0.1%;">Currency</th>
						<th style="width: 1%;">PIC</th>
					</tr>
				</thead>
				<tbody id="tableDetailBody">
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchMonitoring();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var trade_agreements = "";

	function fetchMonitoring(){
		var data = {

		}
		$.get('{{ url("fetch/trade_agreement_list") }}', data, function(result, status, xhr){
			if(result.status){
				var count_active = 0;
				var count_inactive = 0;

				var tableDetailBody = "";
				$('#tableDetailBody').html("");
				trade_agreements = result.trade_agreements;
				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				var cnt = 0;
				var vendor = [];

				$.each(result.trade_agreements, function(key, value){
					if(jQuery.inArray(value.vendor_name, vendor) !== -1){
						
					}
					else{
						cnt += 1;
						if(value.pgr == 'G08'){
							count_active += 1;
						}
						if(value.pgr == 'G15'){
							count_inactive += 1;
						}
						vendor.push(value.vendor_name);
					}

					tableDetailBody += "<tr>";
					tableDetailBody += "<td style='text-align: center; width: 0.1%;'>"+cnt+"</td>";
					tableDetailBody += '<td style="width: 3%;">'+value.vendor_name+'</td>';
					tableDetailBody += '<td style="text-align: center; width: 0.1%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.vendor_code+'\')">'+value.vendor_code+'</a></td>';
					tableDetailBody += "<td style='width: 0.1%;'>"+value.location+"</td>";
					tableDetailBody += "<td style='width: 0.5%;'>"+value.category+"</td>";
					tableDetailBody += "<td style='width: 1%;'>"+value.pgr_name+"</td>";
					tableDetailBody += "<td style='width: 0.1%;'>"+value.currency+"</td>";
					tableDetailBody += "<td style='width: 1.5%;'>"+value.pic_id+"<br>"+value.pic_name+"</td>";
					tableDetailBody += "</tr>";
				});

				$('#count_active').text(count_active);
				$('#count_inactive').text(count_inactive);

				$('#tableDetailBody').append(tableDetailBody);

				Highcharts.chart('container1', {
					chart: {
						backgroundColor: null,
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45,
							beta: 0
						}
					},
					title: {
						text: ''
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					legend: {
						enabled: false,
						symbolRadius: 1,
						borderWidth: 1
					},
					credits:{
						enabled:false
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							edgeWidth: 1,
							edgeColor: 'rgb(126,86,134)',
							borderColor: 'rgb(126,86,134)',
							depth: 35,
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}<br>{point.y} item(s)</b><br>{point.percentage:.1f} %',
								style:{
									fontSize:'0.8vw'
								},
								color:'black',
								connectorWidth: '3px'
							},
							showInLegend: true,
						}
					},
					series: [{
						type: 'pie',
						data: [{
							name: 'Direct Material',
							y: count_active,
							color: '#90ee7e'
						}, {
							name: 'Indirect Material',
							y: count_inactive,
							color: '#555555'
						}]
					}]
				});

				$('#tableDetail').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 25, 50, -1 ],
					[ '25 rows', '50 rows', 'Show all' ]
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
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				MergeGridCells();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed.');
			}
		});

}

function MergeGridCells() {
	var dimension_cells = new Array();
	var dimension_col = null;
	// var columnCount = $("#tableDetail tr:first th").length;
	var columnCount = 4;
	for (dimension_col = 0; dimension_col < columnCount; dimension_col++) {
        // first_instance holds the first instance of identical td
        var first_instance = null;
        var rowspan = 1;
        // iterate through rows
        $("#tableDetail").find('tr').each(function () {

            // find the td of the correct column (determined by the dimension_col set above)
            var dimension_td = $(this).find('td:nth-child(' + dimension_col + ')');

            if (first_instance == null) {
                // must be the first row
                first_instance = dimension_td;
            } else if (dimension_td.text() == first_instance.text()) {
                // the current td is identical to the previous
                // remove the current td
                dimension_td.remove();
                ++rowspan;
                // increment the rowspan attribute of the first instance
                first_instance.attr('rowspan', rowspan);
            } else {
                // this cell is different from the last
                first_instance = dimension_td;
                rowspan = 1;
            }
        });
    }
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '5000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '5000'
	});
}
</script>

@endsection