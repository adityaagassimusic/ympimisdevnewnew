@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
  border: 1px solid #eeeeee !important;
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

	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;padding-left: 0px">
			<div class="row" style="margin:0px;">
				<div class="col-xs-4 col">
					<input type="text" name="tag" id="tag" style="font-size: 20px;height:50px;width: 100%;text-align: center;" placeholder="Scan Kartu Lama" class="form-control">
				</div>
				<div class="col-xs-4">
					<input type="text" name="tag_new" id="tag_new" style="font-size: 20px;height:50px;width: 100%;text-align: center;" placeholder="Scan Kartu Baru" class="form-control">
				</div>
				<div class="col-xs-4">
					<button style="font-weight: bold;font-size: 20px;width: 100%;height: 50px;" onclick="cancelAll();" class="btn btn-danger">CANCEL</button>
				</div>
			</div>
			<div class="row" style="margin:0px;margin-top: 20px;">
				<div class="col-xs-12">
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
			            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
			                <tr>
			                  <th style="width: 1%;">#</th>
			                  <th style="width: 5%;">Kartu Lama</th>
			                  <th style="width: 5%;">Kartu Baru</th>
			                  <th style="width: 5%;">Material</th>
			                  <th style="width: 5%;">Form ID</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function cancelAll() {
		$('#tag').val('');
		$('#tag_new').val('');
		$('#tag').focus();
	}

	jQuery(document).ready(function(){
		$('#tag').val('');
		$('#tag_new').val('');
		$('#tag').focus();
		fillList();
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#tag').val() != '') {
				$('#loading').show();
				var data = {
					tag : $("#tag").val(),
				}

				$.get('{{ url("scan/pn/card_migration_check") }}', data, function(result, status, xhr){
					if(result.status){
						if (result.datas) {
							$('#loading').hide();
							openSuccessGritter('Success!','Kartu Ditemukan');
							$('#tag').focus();
							$('#tag_new').val('');
							$('#tag_new').focus();
						}else{
							$('#loading').hide();
							openErrorGritter('Error!','Kartu Tidak Ditemukan');
							$('#tag_new').val('');
							$('#tag').val('');
							$('#tag').focus();
						}
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error', result.message);
						$('#tag_new').val('');
						$('#tag').removeAttr('disabled');
						$('#tag').val('');
						$('#tag').focus();
					}
				});
			}else{
				$('#loading').hide();
				cancelAll();
			}
		}
	});

	$('#tag_new').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#tag_new').val() != '') {
				$('#loading').show();
				var data = {
					tag : $("#tag").val(),
					tag_new : $("#tag_new").val(),
				}

				$.get('{{ url("scan/pn/card_migration") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!','Migrasi Sukses');
						$('#tag_new').val('');
						$('#tag').val('');
						$('#tag').focus();
						fillList();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error', result.message);
						$('#tag_new').val('');
						$('#tag_new').focus();
					}
				});
			}else{
				$('#loading').hide();
				openErrorGritter('Error', 'Tag Kosong');
				$('#tag_new').val('');
				$('#tag_new').focus();
			}
		}
	});

	function fillList(){
		$('#loading').show();
		$.get('{{ url("fetch/pn/card_migration") }}', function(result, status, xhr){
			if(result.status){
				$('#resultScan').DataTable().clear();
  			    $('#resultScan').DataTable().destroy();

				$('#resultScanBody').html("");
				var tableData = "";
				var index = 1;
				for(var i = 0; i < result.datas.length;i++){
					tableData += '<tr>';
					tableData += '<td style="padding-left:10px !important;border:1px solid black !important;background-color: #f0f0ff;text-align:center;">'+index+'</td>';
					tableData += '<td style="padding-left:10px !important;border:1px solid black !important;background-color: #f0f0ff;text-align:left;">'+result.datas[i].tag_old+'</td>';
					tableData += '<td style="padding-left:10px !important;border:1px solid black !important;background-color: #f0f0ff;text-align:left;">'+result.datas[i].tag_new+'</td>';
					tableData += '<td style="padding-left:10px !important;border:1px solid black !important;background-color: #f0f0ff;text-align:left;">'+result.datas[i].model+'</td>';
					tableData += '<td style="padding-left:10px !important;border:1px solid black !important;background-color: #f0f0ff;text-align:left;">'+result.datas[i].form_id+'</td>';
					tableData += '</tr>';
					index++;
				}

				$('#resultScanBody').append(tableData);

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
		            'searching': true ,
		            'ordering': true,
		            'order': [],
		            'info': true,
		            'autoWidth': true,
		            "sPaginationType": "full_numbers",
		            "bJQueryUI": true,
		            "bAutoWidth": false,
		            "processing": true
		          });

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
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

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection