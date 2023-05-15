@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 20px;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}

#tableMaster > tbody > tr > td > p > img {
      width: 100px !important;
    }
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
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
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
					<div style="text-align: center;background-color: lightskyblue;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: black;font-size: 20px">
							WASHING WAITING TIME
						</span>
					</div>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="col-xs-6" style="padding-right:0px;margin-top: 10px;">
								<span style="font-weight: bold;">Date From</span>
								<div class="form-group">
									<input type="text" name="date_from" id="date_from" placeholder="Select Date From" class="form-control datepicker">
								</div>
							</div>
							<div class="col-xs-6" style="padding-right:0px;margin-top: 10px;">
								<span style="font-weight: bold;">Date To</span>
								<div class="form-group">
									<input type="text" name="date_to" id="date_to" placeholder="Select Date To" class="form-control datepicker">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group pull-right">
								<a href="{{ url('index/process_welding_sx/') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/welding/washing_waiting_time')}}" class="btn btn-danger">Clear</a>
								<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;" id="headTableMaster">
									
								</thead>
								<tbody id="bodyTableMaster">
								</tbody>
								<tfoot id="footTableMaster">
									
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div style="text-align: center;background-color: orange;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: black;font-size: 20px">
							KANBAN HISTORY
						</span>
					</div>
					<div style="text-align: center;margin-bottom: 20px">
						<input type="text" name="tag" id="tag" class="form-control" style="width: 100%;text-align: center;font-size: 20px;" placeholder="Scan Kanban di Sini...">
					</div>
					<div class="col-xs-12">
						<div class="row">
							<table id="tableHistory" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th style="text-align: center;width: 1%">#</th>
										<th style="text-align: center;width: 1%">Tag</th>
										<th style="text-align: center;width: 1%">No. Kanban</th>
										<th style="text-align: center;width: 1%">Barcode</th>
										<th style="text-align: center;width: 8%">Material</th>
										<th style="text-align: center;width: 1%">Model</th>
										<th style="text-align: center;width: 1%">Key</th>
										<th style="text-align: center;width: 1%">Quantity</th>
										<th style="text-align: center;width: 2%">Process</th>
										<th style="text-align: center;width: 2%">NG</th>
										<th style="text-align: center;width: 1%">Started At</th>
										<th style="text-align: center;width: 1%">Finished At</th>
										<th style="text-align: center;width: 3%">Operator</th>
									</tr>
								</thead>
								<tbody id="bodyTableHistory">
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillList();

		$('#tag').val('');
		$('#tag').focus();

		$('.select2').select2({
			allowClear:true,
		});
	});

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
	function fillList(){
		$('#loading').show();

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/welding/washing_waiting_time") }}',data, function(result, status, xhr){
			if(result.status){

				$('#headTableMaster').html('');
				var headTable = '';
				headTable += '<tr>';
					headTable += '<th style="text-align: center;width: 1%">#</th>';
					headTable += '<th style="text-align: center;width: 1%">Tag</th>';
					headTable += '<th style="text-align: center;width: 1%">No. Kanban</th>';
					headTable += '<th style="text-align: center;width: 8%">Material</th>';
					headTable += '<th style="text-align: center;width: 2%">Alias</th>';
					headTable += '<th style="text-align: center;width: 1%">Quantity</th>';
					headTable += '<th style="text-align: center;width: 3%">Washing Time</th>';
	                headTable += '<th style="text-align: center;width: 1%">Welding Time</th>';
	                headTable += '<th style="text-align: center;width: 1%">Diff (Min)</th>';
				headTable += '</tr>';
				$('#headTableMaster').append(headTable);

				$('#footTableMaster').html('');
				var footTable = '';

				footTable += '<tr>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
					footTable += '<th></th>';
				footTable += '</tr>';

				$('#footTableMaster').append(footTable);

				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();

				$('#bodyTableMaster').html("");
				var tableData = "";
				var index = 1;
				$.each(result.washing, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.tags +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.no_kanban +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.material_number +' - '+ value.material_description +' ('+ value.material_category +' - '+ value.material_type +')</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_alias || '') +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.quantity +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.finished_at +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.phs_hsa +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.diff +'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableMaster').append(tableData);

				$('#tableMaster tfoot th').each( function () {
			        var title = $(this).text();
			        $(this).html( '<input style="text-align: center;color:black;" type="text" placeholder="Search '+title+'" size="20"/>' );
			    } );

				var table = $('#tableMaster').DataTable({
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

			      $('#tableMaster tfoot tr').appendTo('#tableMaster thead');

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	$('#tag').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#tag').val().length > 9) {
				$('#loading').show();
				var data = {
					tag:$('#tag').val()
				}

				$.get('{{ url("fetch/welding/kanban_history") }}',data, function(result, status, xhr){
					if(result.status){
						if (result.data.length > 0) {
							var index = 1;

							$('#bodyTableHistory').html('');
							var bodyTableHistory = '';

							var datas = '';

							for(var i = 0; i < result.data.length;i++){
								bodyTableHistory += '<tr>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+index+'</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].tags+'</td>';
								bodyTableHistory += '<td style="text-align:right;padding-right:5px;">'+result.data[i].no_kanban+'</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].barcode+'</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].material_number+' - '+result.data[i].material_description+' ('+result.data[i].material_category+' - '+result.data[i].material_type+')</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].model+'</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].key+'</td>';
								bodyTableHistory += '<td style="text-align:right;padding-right:5px;">'+result.data[i].quantity+'</td>';
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].location+'</td>';
								if (result.data[i].ng != '') {
									if (result.data[i].ng.match(/,/gi)) {
										bodyTableHistory += '<td style="text-align:left;padding-left:5px;">';
										var ngs = result.data[i].ng.split(',');
										for(var j = 0; j < ngs.length;j++){
											bodyTableHistory += ngs[j].split('_')[0]+' = '+ngs[j].split('_')[1]+'<br>';
										}
										bodyTableHistory += '</td>';
									}else{
										bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].ng.split('_')[0]+' = '+result.data[i].ng.split('_')[1]+'</td>';
									}
								}else{
									bodyTableHistory += '<td style="text-align:left;padding-left:5px;"></td>';
								}
								bodyTableHistory += '<td style="text-align:right;padding-right:5px;">'+result.data[i].started_at+'</td>';
								bodyTableHistory += '<td style="text-align:right;padding-right:5px;">'+result.data[i].finished_at+'</td>';
								var name = '';
								for(var j = 0; j < result.emp.length;j++){
									if (result.data[i].last_check == result.emp[j].employee_id) {
										name = result.emp[j].name;
									}
								}
								bodyTableHistory += '<td style="text-align:left;padding-left:5px;">'+result.data[i].last_check+' - '+name+'</td>';
								bodyTableHistory += '</tr>';
								index++;
							}

							$('#bodyTableHistory').append(bodyTableHistory);
							$('#loading').hide();
							$('#tag').val('');
							$('#tag').focus();
						}else{
							$('#loading').hide();
							openErrorGritter('Error!','Tag Invalid');
							$("#tag").val('');
							$("#tag").focus();
						}
					}else{
						$('#loading').hide();
						openErrorGritter('Error!',result.message);
						$("#tag").val('');
						$("#tag").focus();
					}
				});
			}else{
				// $('#loading').hide();
				// openErrorGritter('Error!','Tag Invalid');
				// $("#tag").val('');
				// $("#tag").focus();
			}
		}
	});



</script>
@endsection