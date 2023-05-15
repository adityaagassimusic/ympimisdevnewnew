@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }} <span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-md-12">
			<center style="background-color: rgb(126,86,134); color: #FFD700;font-size: 20px;font-weight: bold;">
				<span>
	            	LIST TEMUAN
				</span>
			</center>
			<div class="box box-solid">
				<div class="box-body">
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
			            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
			                <tr>
			                  <th style="width: 1%;">Lokasi</th>
			                  <th style="width: 2%;">Point Check</th>
			                  <th style="width: 1%;">Tanggal Audit</th>
			                  <th style="width: 3%;">Image</th>
			                  <th style="width: 4%;">Note</th>
			                  <th style="width: 2%;">Leader</th>
			                  <th style="width: 2%;">Action</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
		        </div>
		    </div>
		</div>
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-body">
					<center><span style="font-size: 25px;text-align: center;font-weight: bold;">PENANGANAN</span> </center>
					<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
						<thead style="background-color: rgba(126,86,134,.7);color: white;">
							<tr>
								<th style="width: 1%;">Lokasi</th>
				                <th style="width: 2%;">Point Check</th>
				                <th style="width: 1%;">Tanggal Audit</th>
				                <th style="width: 3%;">Image</th>
				                <th style="width: 4%;">Note</th>
				                <th style="width: 2%;">Leader</th>
				                <th style="width: 2%;">Foto Penanganan</th>
				                <th style="width: 2%;">Hasil Penanganan</th>
				                <th style="width: 2%;">Action</th>
							</tr>
						</thead>
						<tbody id="tableHistoryBody">
						</tbody>
					</table>
				</div>
			</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var counter = 0;
	var arrPart = [];
	var intervalCheck;

	jQuery(document).ready(function() {
		fillAudit();

      $('body').toggleClass("sidebar-collapse");
	});

	function fillAudit() {
		$('#loading').show();

		$.get('{{ url("fetch/maintenance/steam") }}', function(result, status, xhr){
			if(result.status){
				$('#resultScan').DataTable().clear();
				$('#resultScan').DataTable().destroy();
				$('#resultScanBody').html("");
				var datas = '';

				for(var i = 0; i < result.audit.length;i++){
					datas += '<tr>';
					datas += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit[i].location+'</td>';
					datas += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit[i].point_check+'</td>';
					datas += '<td style="background-color:white;text-align:right;padding-right:7px;">'+result.audit[i].date+'</td>';
					var url = '{{url("data_file/daily_audit/steam/")}}/'+result.audit[i].evidence;
					datas += '<td style="background-color:white;"><img src="'+url+'" style="width:100px"></td>';
					datas += '<td style="background-color:white;text-align:left;padding-left:7px;">'+(result.audit[i].note || '')+'</td>';
					datas += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit[i].auditor_name+'</td>';
					datas += '<td style="background-color:white;vertical-align:middle"><button class="btn btn-success" onclick="startDoing(\''+result.audit[i].id+'\')">Kerjakan</button></td>';
					datas += '</tr>';
				}

				$('#resultScanBody').append(datas);

				var table = $('#resultScan').DataTable({
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

				$('#tableHistoryBody').html("");
				var datas2 = '';

				for(var i = 0; i < result.audit_kerjakan.length;i++){
					datas2 += '<tr>';
					datas2 += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit_kerjakan[i].location+'</td>';
					datas2 += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit_kerjakan[i].point_check+'</td>';
					datas2 += '<td style="background-color:white;text-align:right;padding-right:7px;">'+result.audit_kerjakan[i].date+'</td>';
					var url = '{{url("data_file/daily_audit/steam/")}}/'+result.audit_kerjakan[i].evidence;
					datas2 += '<td style="background-color:white;"><img src="'+url+'" style="width:100px"></td>';
					datas2 += '<td style="background-color:white;text-align:left;padding-left:7px;">'+(result.audit_kerjakan[i].note || '')+'</td>';
					datas2 += '<td style="background-color:white;text-align:left;padding-left:7px;">'+result.audit_kerjakan[i].auditor_name+'</td>';
					datas2 += '<td style="background-color:white;vertical-align:middle"><input type="file" accept="image/*" capture="environment" id="handling_evidence_'+result.audit_kerjakan[i].id+'"></td>';
					datas2 += '<td style="background-color:white;vertical-align:middle;padding:0px;"><textarea style="width:100%" id="handling_'+result.audit_kerjakan[i].id+'"></textarea></td>';
					datas2 += '<td style="background-color:white;vertical-align:middle"><button class="btn btn-danger" onclick="finishDoing(\''+result.audit_kerjakan[i].id+'\')">Selesai</button></td>';
					datas2 += '</tr>';
				}

				$('#tableHistoryBody').append(datas2);
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function startDoing(id) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("doing/maintenance/steam") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Mulai Mengerjakan');
					fillAudit();
				}
				else{
					fillAudit();
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			});
	}

	function finishDoing(id) {
			$('#loading').show();
			if ($('#handling_evidence_'+id).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Upload Foto Penanganan');
				return false;
			}

			if ($('#handling_'+id).val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!','Isi Hasil Penanganan');
				return false;
			}

			var file = $('#handling_evidence_'+id).prop('files')[0];
			var filename = $('#handling_evidence_'+id).val().replace(/C:\\fakepath\\/i, '').split(".")[0];
			var extension = $('#handling_evidence_'+id).val().replace(/C:\\fakepath\\/i, '').split(".")[1];

			var formData = new FormData();
			formData.append('id',id);
			formData.append('handling',$('#handling_'+id).val());
			formData.append('file',file);
			formData.append('filename',filename);
			formData.append('extension',extension);

			$.ajax({
				url:"{{ url('input/maintenance/steam') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						fillAudit();
						$('#loading').hide();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
	}

	function cancelTag(){
		location.reload();
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection