@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	#loading, #error { display: none; }
	#bodyJigChildTable {
		height:120px;
		overflow-y: scroll;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-3" style="padding: 0 0 0 10px;">
			<table style="width: 100%; text-align: center; background-color: #5cdfff; font-weight: bold;" border="1">
				<thead>
					<tr>
						<th colspan="2" style="text-align: center;">Operator Repair Jig Proses</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%;" id="op_id">{{$employee_id}}</td>
						<td style="width: 8%;" id="op_name">{{$name}}</td>
					</tr>
				</tbody>
			</table>
			<table style="width: 100%; text-align: center; background-color: #ffbc42; font-weight: bold; " border="1">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center;">Informasi Jig</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 3%;" id="jigID">-</td>
						<td style="width: 3%;" id="jigIndex">-</td>
						<td style="width: 8%;" id="jigName">-</td>
					</tr>
				</tbody>
			</table>
			<table style="width: 100%; background-color: white; font-weight: bold;" border="1" id="drawingTable">
				<thead>
					<th style="background-color:rgb(126,86,134);color:white;text-align: center; width: 1%; color: #FFD700;">Drawing List</th>
				</thead>
				<tbody id="drawingTableBody" style="background-color: rgb(236, 240, 245)">
					<tr>
						<td style="padding-bottom: 5px" id="drawing"></td>
					</tr>
				</tbody>
			</table>
			<!-- <table style="width: 100%; margin-bottom: 2px;" border="1" id="jigChildTable">
				<thead>
					<tr>
						<th colspan="7" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;"><center>Order Part To Workshop</center></th>
					</tr>
					<tr>
						<input type="hidden" id="loop">
						<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>Jig</center></th>
						<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>Part</center></th>
						<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>Stock</center></th>
						<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>Min</center></th>
						<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>#</center></th>
						<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>Count</center></th>
						<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" ><center>#</center></th>
					</tr>
				</thead>
				<tbody id="bodyJigChildTable">
				</tbody>
			</table> -->
		</div>
		<div class="col-xs-9" style="padding: 0 10px 0 10px;">
			<table style="width: 100%; background-color: rgb(236, 240, 245); font-weight: bold; font-size: 1.3vw;" border="1" id="kensaTable">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="text-align: center; width: 1%;" colspan="8">HASIL KENSA</th>
					</tr>
					<tr>
						<th style="text-align: center; width: 1%;">#</th>
						<th style="text-align: center; width: 9%;">Point Check</th>
						<th style="text-align: center; width: 8%;">Part</th>
						<!-- <th style="text-align: center; width: 3%;">Jig Alias</th> -->
						<th style="text-align: center; width: 2%;">Min</th>
						<th style="text-align: center; width: 2%;">Max</th>
						<th style="text-align: center; width: 2%;">Value</th>
						<th style="text-align: center; width: 2%;">Result</th>
						<th style="text-align: center; width: 5%;">Action</th>
					</tr>
				</thead>
				<tbody id="kensaTableBody" style="background-color: rgb(236, 240, 245)">
				</tbody>
			</table>
			<div class="col-xs-6" style="padding-left: 0;padding-right: 5px;padding-top: 5px">
				<button class="btn btn-danger" style="width: 100%; font-size: 2vw; font-weight: bold; padding:0;" onclick="backToBefore()">CANCEL</button>
			</div>
			<div class="col-xs-6" style="padding-right: 0;padding-left: 5px;padding-top: 5px">
				<button class="btn btn-success" onclick="confirmRepair()" style="width: 100%; font-size: 2vw; font-weight: bold; padding:0;">CONFIRM</button>
			</div>
		</div>
		<!-- <div class="col-xs-12" style="margin-top: 5px;padding-top: 5px;padding-left: 10px;padding-right: 10px">
		</div> -->
		<div class="col-xs-12" style="margin-top: 5px;padding-top: 5px">
			<div class="row">
				<div class="col-xs-6" style="padding-left: 10px;padding-right: 5px;">
					<table class="table table-bordered">
						<tr>
							<td style="font-weight: bold;text-align: center;background-color: #ffa3a3">Foto Sebelum</td>
						</tr>
						<tr>
							<td style="text-align: center;background-color: #fff"><input type="file" accept="image/*" capture="environment" id="image_before" onchange="readURL(this,'image_before');"></td>
						</tr>
						<tr>
							<td style="text-align: center;background-color: #fff"><img width="250px" id="image_before_evidence" src="" style="display: none" alt="your image" /></td>
						</tr>
					</table>
				</div>
				<div class="col-xs-6" style="padding-right: 10px;padding-left: 5px;">
					<table class="table table-bordered">
						<tr>
							<td style="font-weight: bold;text-align: center;background-color: #acffa3">Foto Sesudah</td>
						</tr>
						<tr>
							<td style="text-align: center;background-color: #fff"><input type="file" accept="image/*" capture="environment" id="image_after" onchange="readURL(this,'image_after');"></td>
						</tr>
						<tr>
							<td style="text-align: center;background-color: #fff"><img width="250px" id="image_after_evidence" src="" style="display: none" alt="your image" /></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 5px;padding-top: 5px">
			<div id="drawingDetail">
				
			</div>
		</div>
	</div>
<input type="hidden" id="employee_id" value="{{$employee_id}}">
<input type="hidden" id="jig_id">
<input type="hidden" id="jig_index">
<input type="hidden" id="started_at">
<input type="hidden" id="check_index">
</section>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var jig = JSON.parse('<?php echo $jig ?>');

	jQuery(document).ready(function() {
		// $('#modalOperator').modal({
		// 	backdrop: 'static',
		// 	keyboard: false
		// });

		// $('#modalOperator').on('shown.bs.modal', function () {
		// 	$('#operator').focus();
		// });

		// $('#operator').val("");
		// $('#tagJig').prop('disabled',true);
		// $('#tagJig').val('');
		fetchDrawingList('{{$jig_id}}');

		$('#jigID').text('{{$jig_id}}');
		$('#jigIndex').text('{{$jig[0]->jig_index}}');
		$('#jig_id').val('{{$jig[0]->jig_id}}');
		$('#jig_index').val('{{$jig[0]->jig_index}}');
		$('#jigName').text('{{$jig[0]->jig_name}}');
		$('#started_at').val('{{date("Y-m-d H:i:s")}}');

		fetchCheckList();
	});

	function backToBefore() {
		if ('{{$from}}' == 'home') {
			window.location.href = "{{url('index/welding_jig')}}";
		}
	}

	function fetchCheckList() {
		var check_index = 0;
		var index = 1;

		$.each(jig, function(key, value) {

			if (value.result == 'NG') {
				var color = '#ff8282';
			}else{
				var color = '#7fff6e';
			}

			kensaTable += '<tr style="padding:2px;background-color:'+color+'" id="check_index_'+value.check_index+'">';
			kensaTable += '<td><center>'+index+'</center></td>';
			kensaTable += '<td><center id="check_name_'+value.check_index+'">'+value.check_name+'</center></td>';
			kensaTable += '<td style="font-size:15px;font-weight:bold"><center><span id="part_'+value.check_index+'">'+value.jig_child+'</span> - <span id="jig_alias_'+value.check_index+'">'+value.jig_alias+'</span></center></td>';
			// kensaTable += '<td><center id="jig_alias_'+value.check_index+'">'+value.jig_alias+'</center></td>';
			kensaTable += '<td><center id="lower_'+value.check_index+'">'+value.lower_limit+'</center></td>';
			kensaTable += '<td><center id="upper_'+value.check_index+'">'+value.upper_limit+'</center></td>';
			kensaTable += '<td><center id="value_'+value.check_index+'">'+value.value+'</center></td>';
			kensaTable += '<td><center id="result_'+value.check_index+'">'+value.result+'</center></td>';
			if (value.result == 'NG') {
				kensaTable += '<td><select style="width:100%;text-align:center" name="action_'+value.check_index+'" id="action_'+value.check_index+'" onchange="getPart(this.value,\'part_'+value.check_index+'\')"><option value="-">-</option><option value="OK">OK</option><option value="Open">Open</option></select></td>';
			}else{
				kensaTable += '<td><select style="width:100%;text-align:center" name="action_'+value.check_index+'" id="action_'+value.check_index+'"><option value="OK">OK</option><option value="-">-</option></select></td>';
			}
			kensaTable += '</tr>';
			check_index = value.check_index;
			index++;
		});

		$('#kensaTableBody').append(kensaTable);

		$('#check_index').val(check_index);

		$.each(jig, function(key, value) {
			if (value.action != null) {
				$('#action_'+value.check_index).val(value.action).trigger('change');
			}
		});
	}

	function readURL(input,idfile) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#'+idfile+'_evidence').show();
              $('#'+idfile+'_evidence')
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

	// $('#tagJig').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#tagJig").val().length == 10){
	// 			var data = {
	// 				tag : $("#tagJig").val(),
	// 				category : 'KENSA',
	// 				status : 'Repair'
	// 			}
	// 			$.get('{{ url("scan/welding/jig") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					var kensaTable = "";
	// 					$('#kensaTableBody').empty();
	// 					var valueCheck = [];
	// 					var index = 1;
	// 					var check_index = 0;

	// 					var jig_id;

	// 					$.each(result.jig, function(key, value) {

	// 						if (value.result == 'NG') {
	// 							var color = '#ff8282';
	// 						}else{
	// 							var color = '#7fff6e';
	// 						}

	// 						kensaTable += '<tr style="padding:2px;background-color:'+color+'" id="check_index_'+value.check_index+'">';
	// 						kensaTable += '<td><center>'+index+'</center></td>';
	// 						kensaTable += '<td><center id="check_name_'+value.check_index+'">'+value.check_name+'</center></td>';
	// 						kensaTable += '<td style="font-size:15px;font-weight:bold"><center><span id="part_'+value.check_index+'">'+value.jig_child+'</span> - <span id="jig_alias_'+value.check_index+'">'+value.jig_alias+'</span></center></td>';
	// 						// kensaTable += '<td><center id="jig_alias_'+value.check_index+'">'+value.jig_alias+'</center></td>';
	// 						kensaTable += '<td><center id="lower_'+value.check_index+'">'+value.lower_limit+'</center></td>';
	// 						kensaTable += '<td><center id="upper_'+value.check_index+'">'+value.upper_limit+'</center></td>';
	// 						kensaTable += '<td><center id="value_'+value.check_index+'">'+value.value+'</center></td>';
	// 						kensaTable += '<td><center id="result_'+value.check_index+'">'+value.result+'</center></td>';
	// 						if (value.result == 'NG') {
	// 							kensaTable += '<td><select style="width:100%;text-align:center" name="action_'+value.check_index+'" id="action_'+value.check_index+'" onchange="getPart(this.value,\'part_'+value.check_index+'\')"><option value="-">-</option><option value="OK">OK</option><option value="Open">Open</option></select></td>';
	// 						}else{
	// 							kensaTable += '<td><select style="width:100%;text-align:center" name="action_'+value.check_index+'" id="action_'+value.check_index+'"><option value="OK">OK</option><option value="-">-</option></select></td>';
	// 						}
	// 						kensaTable += '</tr>';
	// 						check_index = value.check_index;
	// 						index++;
	// 					});

	// 					$('#kensaTableBody').append(kensaTable);

	// 					$('#check_index').val(check_index);

	// 					$.each(result.jig, function(key, value) {
	// 						if (value.action != null) {
	// 							$('#action_'+value.check_index).val(value.action).trigger('change');
	// 						}
	// 					});

						
	// 				}
	// 				else{
	// 					audio_error.play();
	// 					openErrorGritter('Error', result.message);
	// 					$('#tagJig').val('');
	// 					$('#tagJig').focus();
	// 				}
	// 			});
	// 		}
	// 		else{
	// 			audio_error.play();
	// 			openErrorGritter('Error', 'Tag Tidak Ditemukan');
	// 			$('#tagJig').val('');
	// 			$('#tagJig').focus();
	// 		}
	// 	}
	// });

	function plus(id){
		var count = $('#count_'+id).text();
		$('#count_'+id).text(parseInt(count)+1);
	}

	function minus(id){
		var count = $('#count_'+id).text();
		if(count > 0)
		{
			$('#count_'+id).text(parseInt(count)-1);
		}
	}

	function getPart(value,id_part) {
		// if (value !== '-') {
		// 	var loop = $('#loop').val();
		// 	for(var i = 1; i < loop;i++){
		// 		if (document.getElementById('count_'+i).getAttribute('name') == $('#'+id_part).text()) {
		// 			var count = $('#count_'+i).text();
		// 			$('#count_'+i).text(parseInt(count)+1);
		// 		}
		// 	}
		// }
	}

	function cancelProcess() {
		// $('#tagJig').removeAttr('disabled');
		// $('#tagJig').val('');
		// $('#tagJig').focus();
		$('#jigID').html("-");
		$('#jigIndex').html("-");
		$('#jigName').html("-");
		$('#jig_id').val("");
		$('#jig_index').val("");
		$('#started_at').val("");
		$('#kensaTableBody').empty();
		$('#drawing').empty();
		$('#drawingDetail').empty();
		$('#bodyJigChildTable').empty();
	}

	function fetchDrawingList(jig_id) {
		var data = {
			jig_id : jig_id
		}
		$.get('{{ url("fetch/welding/drawing_list_proses") }}', data, function(result, status, xhr){
			if(result.status){
				var drawing = "";
				$('#drawing').empty();
				var index = 1;
				$.each(result.drawing, function(key, value) {
					var jig_childs = value.jig_child.split('-');
					var jig_names = value.jig_name.split(' ');
					drawing += '<div class="col-xs-4" style="padding-left:5px;padding-right:5px;margin-top:5px"><button class="btn btn-primary" onclick="fetchDrawing(\''+value.jig_parent+'\',\''+value.file_name+'\',\''+result.file_path+'\')" style="height:40px;width:100%;white-space: normal;padding:0px"><b>'+jig_childs[2]+'-'+jig_childs[3]+'<br>'+jig_names[3]+' '+jig_names[4]+'</b></button></div>';
					index++;
				});
				drawing += '<div class="col-xs-4" style="padding-left:5px;padding-right:5px;margin-top:5px"><button class="btn btn-danger" onclick="closeDrawing()" style="height:40px;width:100%;white-space: normal;padding:0px"><b>CLOSE<br>DRAWING</b></button></div>';

				$('#drawing').append(drawing);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function closeDrawing() {
		$('#drawingDetail').empty();
	}

	function fetchDrawing(jig_parent,file_name,file_path) {
		$('#drawingDetail').empty();
		// console.log(file_name);
		var result = doesFileExist(file_path +"/"+ jig_parent +"/"+ file_name);
		if (result == true) {
			if(file_name.includes('.pdf')){
				$('#drawingDetail').append("<embed src='"+ file_path +"/"+ jig_parent +"/"+ file_name +"' type='application/pdf' width='100%' height='600px'>");
			}
		} else {
		    $('#drawingDetail').append("<center><span style='font-weight:bold;font-size:30px;color:white'>Tidak Ada Drawing</span></center>");
		}
	}

	function doesFileExist(urlToFile) {
	    var xhr = new XMLHttpRequest();
	    xhr.open('HEAD', urlToFile, false);
	    xhr.send();
	     
	    if (xhr.responseURL.includes('404')) {
	        return false;
	    } else {
	        return true;
	    }
	}

	function confirmRepair() {
		$('#loading').show();
		var operator_id = $('#employee_id').val();
		var jig_id = $('#jig_id').val();
		var jig_index = $('#jig_index').val();
		var started_at = $('#started_at').val();
		var check_indexes = $('#check_index').val();

		var check_index = [];
		var check_name = [];
		var upper_limit = [];
		var lower_limit = [];
		var value = [];
		var result = [];
		var jig_child = [];
		var jig_alias = [];
		var action = [];

		var jig_parent = [];
		var part = [];
		var count = [];

		var menunggu_part = 0;
		var action_kosong = 0;

		if ($('#image_before').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '' || $('#image_after').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan Foto Before & After');
			audio_error.play();
			return false;
		}

		for(var i = 1; i <= check_indexes;i++ ){
			check_index.push(i);
			check_name.push($('#check_name_'+i).text());
			upper_limit.push($('#upper_'+i).text());
			lower_limit.push($('#lower_'+i).text());
			value.push($('#value_'+i).text());
			result.push($('#result_'+i).text());
			jig_child.push($('#part_'+i).text());
			jig_alias.push($('#jig_alias_'+i).text());
			action.push($('#action_'+i).val());

			if ($('#action_'+i).val() == 'Open') {
				menunggu_part++;
			}

			if ($('#action_'+i).val() == '-') {
				action_kosong++;
			}
		}

		var loop = $('#loop').val();

		for(var i = 1; i < loop;i++){
			if ($('#count_'+i).text() != 0) {
				jig_parent.push($('#jig_parent_'+i).text());
				part.push($('#jig_child_'+i).text());
				count.push($('#count_'+i).text());
			}
		}

		if (action_kosong > 0) {
			$('#loading').hide();
			openErrorGritter('Error!','Action Tidak Boleh Kosong.');
		}else{
			if (menunggu_part > 0) {
				var statuses = 'Open';
			}else{
				var statuses = 'Repaired';
			}

			var formData = new FormData();
			var newAttachment_before  = $('#image_before').prop('files')[0];
			var file_before = $('#image_before').val().replace(/C:\\fakepath\\/i, '').split(".");

			var newAttachment_after  = $('#image_after').prop('files')[0];
			var file_after = $('#image_after').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment_before', newAttachment_before);
			formData.append('extension_before', file_before[1]);
			formData.append('file_name_before', file_before[0]);

			formData.append('newAttachment_after', newAttachment_after);
			formData.append('extension_after', file_after[1]);
			formData.append('file_name_after', file_after[0]);

			formData.append('operator_id',operator_id);
			formData.append('jig_id',jig_id);
			formData.append('jig_index',jig_index);
			formData.append('started_at',started_at);
			formData.append('check_indexes',check_indexes);
			formData.append('check_index',check_index);
			formData.append('check_name',check_name);
			formData.append('upper_limit',upper_limit);
			formData.append('lower_limit',lower_limit);
			formData.append('value',value);
			formData.append('result',result);
			formData.append('jig_child',jig_child);
			formData.append('jig_alias',jig_alias);
			formData.append('action',action);
			formData.append('jig_parent',jig_parent);
			formData.append('part',part);
			formData.append('count',count);
			formData.append('status',statuses);

			$.ajax({
				url:"{{ url('input/welding/repair_jig_proses') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success!',data.message);
						$('#image_before').val("");
						$('#image_after').val("");
						backToBefore();
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});

			// $.post('{{ url("input/welding/repair_jig") }}', data, function(result, status, xhr){
			// 	if(result.status){
			// 		openSuccessGritter('Success!', result.message);
			// 		$('#loading').hide();
			// 		cancelProcess();
			// 	}
			// 	else{
			// 		audio_error.play();
			// 		openErrorGritter('Error', result.message);
			// 		$('#loading').hide();
			// 	}
			// });
		}
	}

	function backToBefore() {
		if ('{{$from}}' == 'home') {
			window.location.href = "{{url('index/welding_jig')}}";
		}
		if ('{{$from}}' == 'monitoring') {
			window.location.href = "{{url('index/welding/monitoring_jig_proses')}}";
		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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