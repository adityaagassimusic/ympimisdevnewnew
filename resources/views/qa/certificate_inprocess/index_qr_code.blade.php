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
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
		padding: 4px;
	}
	tfoot>tr>th{
		text-align:center;
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

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}


.container_checkmark {
  display: block;
  position: relative;
  padding-left: 10px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 20px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<!-- <small class="text-purple">{{ $title_jp }}</small> -->
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>					
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableApproval" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="5%">Certificate No.</th>
										<th width="5%">Desc</th>
										<th width="5%">Emp</th>
										<th width="3%">Print Status</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableApproval">
									<?php $index = 1; ?>
									@foreach($qr_code as $qr_code)
									<tr>
										<td style="text-align: right;padding-right: 3px;padding-left: 3px;width: 1%">{{$index}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 2%">YMPI-QA-{{$qr_code->code}}-{{$qr_code->code_number}}{{$qr_code->number}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 2%">{{$qr_code->certificate_desc}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 3%">{{$qr_code->employee_id}} - {{$qr_code->name}}</td>
											@if($qr_code->print_status == null)
											<td style="padding-right: 3px;padding-left: 3px;width: 3%;background-color:#ffa3a3">
												Belum Cetak
											</td>
											@else
											<td style="padding-right: 3px;padding-left: 3px;width: 3%;background-color:#a3ffbd">
												Sudah Cetak
											</td>
											@endif
										<td style="padding-right: 3px;padding-left: 3px;width: 2%"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Print
											  <input type="checkbox" name="approval" id="approval" class="approval" value="{{$qr_code->certificate_id}}">
											  <span class="checkmark_checkmark"></span>
											</label>
										</td>
										</tr>
										<?php $index++ ?>
									@endforeach
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-xs-12" style="margin-top: 20px;margin-bottom: 20px;">
						<div class="row">
							<button style="width: 100%;font-weight: bold;font-size: 30px" class="btn btn-success btn-xs" onclick="print()"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</button>
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
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#div_attach').hide();
		$('#attach_pdf').html('');
		// fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		var table = $('#tableApproval').DataTable({
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
	});

	function attach_pdf(file_name) {
		$("#div_attach").show();
		$('#attach_pdf').html('');
		var path = "{{asset('/data_file/qa/certificate_fix/')}}"+'/'+file_name+'.pdf';
      	$('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
	}


	$(function () {
		$('.select2').select2({
			allowClear:true
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

	function print() {
		$('#loading').show();
		var approval = [];
		$("input[name='approval']:checked").each(function (i) {
	            approval[i] = $(this).val();
        });
        if (approval.length == 0) {
        	$('#loading').hide();
        	openErrorGritter('Error!','Pilih Sertifikat');
        	return false;
        }
        window.open("{{url('print/qa/qr_code/certificate/inprocess/')}}"+'/'+approval.join(','),'_blank');
        $('#loading').hide();
        location.reload();
	}

	// function printAll() {
	// 	$("input[name='visus_os']").each(function (i) {
	// 		if ($('.id_pkb')[i].checked == true) {
	// 			$('.id_pkb')[i].checked = false;
	// 		}else{
	// 			$('.id_pkb')[i].checked = true;
	// 		}
 //        });
	// }

	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}



</script>
@endsection