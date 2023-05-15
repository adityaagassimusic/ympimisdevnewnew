@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	th:hover {
		overflow: visible;
	}
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif						
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="periode" id="periode" data-placeholder="Pilih Periode" style="width: 100%;">
									<option></option>
									@foreach($periode as $periode)
										<option value="{{$periode->fiscal_year}}">{{$periode->fiscal_year}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/human_resource/let') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/human_resource/let/master') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableLet" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableLet">
								</thead>
								<tbody id="bodyTableLet">
								</tbody>
								<tfoot>
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

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();
		$('#bodyTableLet').html("");
		$('#headTableLet').html("");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
	});

	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
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

	function fillList(){
		$('#loading').show();
		var data = {
			periode:$('#periode').val()
		}
		$.get('{{ url("fetch/human_resource/let/report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableLet').html("");
				$('#headTableLet').html('');

				var tableDataBody = "";
				var tableDataHead = "";
				var index = 1;

				tableDataHead += '<tr>';
				tableDataHead += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">#</th>';
				tableDataHead += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Periode</th>';
				tableDataHead += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">ID</th>';
				tableDataHead += '<th width="5%" style="background-color: rgb(126,86,134); color: #fff;">Name</th>';
				tableDataHead += '<th width="10%" style="background-color: rgb(126,86,134); color: #fff;">Title</th>';
				tableDataHead += '<th width="1%" style="background-color: rgb(126,86,134); color: #fff;">Dept</th>';
				var asesor = [];
				for(var i = 0; i < result.report.length;i++){
					asesor.push(result.report[i].asesor_name);
				}
				var asesor_unik = asesor.filter(onlyUnique);
				for(var i = 0; i < asesor_unik.length;i++){
					tableDataHead += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">'+asesor_unik[i]+'</th>';
				}
				tableDataHead += '<th width="2%" style="background-color: rgb(126,86,134); color: #fff;">Total Nilai</th>';
				tableDataHead += '</tr>';

				$('#headTableLet').append(tableDataHead);

				$('#tableLet').DataTable().clear();
				$('#tableLet').DataTable().destroy();

				var index = 1;

				var datas = [];

				$.each(result.participant, function(key, value) {
					var dept = '';
					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.employee_id) {
							dept = result.emp[i].department_shortname;
						}
					}
					var total_all = 0;
					var totalss = [];
					for(var j = 0; j < asesor_unik.length;j++){
						var totals = 0;
						for(var k = 0; k < result.report.length;k++){
							if (result.report[k].employee_id == value.employee_id && result.report[k].asesor_name == asesor_unik[j]) {
								if (result.report[k].result != null) {
									totals = totals + result.report[k].result;
									total_all = total_all + result.report[k].result;
								}
							}
						}
						totalss.push(totals);
					}
					datas.push({
						'periode':value.periode,
						'employee_id':value.employee_id,
						'name':value.name,
						'title':value.title,
						'dept':dept,
						'totals':totalss,
						'total_all':parseInt(total_all),
					});
				});

				var datas_sort = sortArray(datas, "total_all", -1);

				$.each(datas_sort, function(key, value) {
					tableDataBody += '<tr>';
					tableDataBody += '<td style="padding:10px;text-align:center">'+ index +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.periode +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.employee_id +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.name +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.title +'</td>';
					tableDataBody += '<td style="padding:10px;text-align:left">'+ value.dept +'</td>';
					for(var j = 0; j < asesor_unik.length;j++){
						tableDataBody += '<td style="padding:10px;text-align:center">'+ value.totals[j] +'</td>';
					}
					tableDataBody += '<td style="padding:10px;text-align:center">'+ value.total_all +'</td>';
					tableDataBody += '</tr>';
					index++;
				})
				$('#bodyTableLet').append(tableDataBody);

				var table = $('#tableLet').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					"order": [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }



</script>
@endsection