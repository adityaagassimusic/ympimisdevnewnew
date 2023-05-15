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
		{{ $title }} ~ {{ $remark }} <small class="text-purple">{{ $title_jp }}</small>
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
										<th width="5%">Certificate No.<br><small>認定番号</small></th>
										<th width="5%">Desc<br><small>内容</small></th>
										<th width="3%">From<br><small>有効日付</small></th>
										<th width="3%">To<br><small>無効日付</small></th>
										<th width="5%">Emp<br><small>従業員</small></th>
										<th width="5%">Preview<br><small>プレビュー</small></th>
										<th width="1%">Action<br><small>アクション</small></th>
									</tr>
								</thead>
								<tbody id="bodyTableApproval">
									<?php $index = 1; ?>
									<?php $approval_length = count($approval_now) ?>
									@foreach($approval_now as $app)
									<tr>
										<td style="text-align: right;padding-right: 3px;padding-left: 3px;width: 1%">{{$index}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 2%">YMPI-QA-{{$app->code}}-{{$app->code_number}}{{$app->number}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 2%">{{$app->description}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 1%;text-align: center;">{{date('d M Y',strtotime(explode('_',$app->periode)[0]))}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 1%;text-align: center;">{{date('d M Y',strtotime(explode('_',$app->periode)[1]))}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 3%">{{$app->employee_id}} - {{$app->name}}</td>
										<td style="padding-right: 3px;padding-left: 3px;width: 3%;text-align: center;">
											<button class="btn btn-danger btn-xs" onclick="attach_pdf('{{$app->certificate_id}}')"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Preview <small>プレビュー</small></button></td>
										<td style="padding-right: 3px;padding-left: 3px;width: 2%"><label class="container_checkmark" style="color: green;font-size: 13px;padding-left: 35px;" align="center">Sudah Dicek
											  <input type="checkbox" name="approval" id="approval" class="approval" value="{{$app->certificate_id}}">
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
					<?php if (count($approval_now) == 0) {
						$stylediv = 'display:none';
					}else{
						$stylediv = '';
					} ?>
					<div class="col-xs-12" style="margin-top: 20px;margin-bottom: 20px;<?php echo $stylediv ?>">
						<!-- <div class="col-xs-3 col-xs-offset-5" align="center">
							<center><label class="container_checkmark" style="color: green">Sudah Dicek Semua
							<input type="checkbox" name="print_all" id="print_all" class="print_all" onclick="checkAll(this.checked)">
							<span class="checkmark_checkmark"></span>
							</label></center>
						</div> -->
						<div class="row">
							<button style="width: 100%;font-weight: bold;font-size: 30px" class="btn btn-success btn-xs" onclick="approve()"><i class="fa fa-check"></i>&nbsp;&nbsp;Approve <small>承認</small></button>
						</div>
					</div>
					<div class="col-xs-12" style="display: none" id="div_attach" style="margin-top: 10px">
						<div class="row">
							<button style="width: 100%;font-weight: bold;font-size: 25px" class="btn btn-danger btn-xs" onclick="$('#div_attach').hide();$('#attach_pdf').html('');"><i class="fa fa-close"></i>&nbsp;&nbsp;Close <small>クロス</small></button>
							<div id="attach_pdf"></div>
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
	// function fillList(){

	// 	var data = {
	// 		periode:$('#periode').val(),
	// 	}
	// 	$.get('{{ url("fetch/pkb/report") }}',data, function(result, status, xhr){
	// 		if(result.status){
	// 			$('#tableApproval').DataTable().clear();
	// 			$('#tableApproval').DataTable().destroy();
	// 			$('#bodyTableApproval').html("");
	// 			var tableData = "";
	// 			var index = 1;
	// 			$.each(result.pkb, function(key, value) {
	// 				tableData += '<tr>';
	// 				tableData += '<td>'+ index +'</td>';
	// 				tableData += '<td>'+ value.periode +'</td>';
	// 				tableData += '<td>'+ value.employee_id +'</td>';
	// 				tableData += '<td>'+ value.name+'</td>';
	// 				tableData += '<td>'+ (value.department_shortname || '') +'</td>';
	// 				tableData += '<td>'+ (value.section || '') +'</td>';
	// 				tableData += '<td>'+ (value.group || '') +'</td>';
	// 				tableData += '<td>'+ (value.sub_group || '') +'</td>';
	// 				tableData += '<td>'+ (value.agreement || '')+'</td>';
	// 				tableData += '<td>'+ (value.created || '')+'</td>';
	// 				// var url = '{{url("print/pkb/report")}}/'+value.id_pkb;
	// 				tableData += '<td><label class="container_checkmark" style="color: green">Print';
	// 					  tableData += '<input type="checkbox" name="id_pkb" id="id_pkb" class="id_pkb" value="'+value.id_pkb+'">';
	// 					  tableData += '<span class="checkmark_checkmark"></span>';
	// 					tableData += '</label>';
	// 				tableData += '</td>';
	// 				tableData += '</tr>';
	// 				index++;
	// 			});
	// 			$('#bodyTableApproval').append(tableData);

	// 			var table = $('#tableApproval').DataTable({
	// 				'dom': 'Bfrtip',
	// 				'responsive':true,
	// 				'lengthMenu': [
	// 				[ 10, 25, 50, -1 ],
	// 				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 				],
	// 				'buttons': {
	// 					buttons:[
	// 					{
	// 						extend: 'pageLength',
	// 						className: 'btn btn-default',
	// 					},
	// 					{
	// 						extend: 'copy',
	// 						className: 'btn btn-success',
	// 						text: '<i class="fa fa-copy"></i> Copy',
	// 							exportOptions: {
	// 								columns: ':not(.notexport)'
	// 						}
	// 					},
	// 					{
	// 						extend: 'excel',
	// 						className: 'btn btn-info',
	// 						text: '<i class="fa fa-file-excel-o"></i> Excel',
	// 						exportOptions: {
	// 							columns: ':not(.notexport)'
	// 						}
	// 					},
	// 					{
	// 						extend: 'print',
	// 						className: 'btn btn-warning',
	// 						text: '<i class="fa fa-print"></i> Print',
	// 						exportOptions: {
	// 							columns: ':not(.notexport)'
	// 						}
	// 					}
	// 					]
	// 				},
	// 				'paging': true,
	// 				'lengthChange': true,
	// 				'pageLength': 10,
	// 				'searching': true	,
	// 				'ordering': true,
	// 				'order': [],
	// 				'info': true,
	// 				'autoWidth': true,
	// 				"sPaginationType": "full_numbers",
	// 				"bJQueryUI": true,
	// 				"bAutoWidth": false,
	// 				"processing": true
	// 			});
	// 		}
	// 		else{
	// 			alert('Attempt to retrieve data failed');
	// 		}
	// 	});
	// }

	function approve() {
		$('#loading').show();
		var approval = [];
		$("input[name='approval']:checked").each(function (i) {
	            approval[i] = $(this).val();
        });
        if (approval.length < parseInt('{{$approval_length}}')) {
        	audio_error.play();
        	$('#loading').hide();
        	openErrorGritter('Error!','Sertifikat Harus Disetujui Semua.');
        	return false;
        }
        var data = {
        	certificate_id:approval,
        	remark:'{{$remark}}'
        }
        $.get('{{ url("approve/qa/certificate") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				openSuccessGritter('Success!',result.message);
				location.reload();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		});
        
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