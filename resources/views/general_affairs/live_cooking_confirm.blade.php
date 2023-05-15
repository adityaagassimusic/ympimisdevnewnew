@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	font-size: 0.93vw;
	border:1px solid black;
	padding-top: 5px;
	padding-bottom: 5px;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 2px;
	padding-right: 2px;
	vertical-align: middle;
}
table.table-bordered > tfoot > tr > th{
	font-size: 0.8vw;
	border:1px solid black;
	padding-top: 0;
	padding-bottom: 0;
	vertical-align: middle;
}	
#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-info btn-sm pull-right" style="margin-left: 5px; width: 10%;" onclick="location.reload()"><i class="fa fa-refresh"></i> Refresh</button>
	</h1>
</section>
@endsection

@section('content')
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
			<table class="table table-hover table-bordered table-striped" id="tableConfirm">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="color:white;width: 1%;">No.</th>
						<th style="color:white;width: 1%;">Order By ID</th>
						<th style="color:white;width: 3%;">Order By Name</th>
						<th style="color:white;width: 1%;">Date</th>
						<th style="color:white;width: 1%;">Order For ID</th>
						<th style="color:white;width: 3%;">Order For Name</th>
						<th style="color:white;width: 1%;">Action</th>
					</tr>
				</thead>
				<tbody id="tableConfirmBody">
					<?php $index = 1; ?>
					@foreach($confirm as $confirm)
					<tr id="list_{{$confirm->id}}" style="background-color: #ccff90">
						<td style="text-align: center;">{{$index}}</td>
						<td>{{$confirm->order_by}}</td>
						<?php $name_by = ''; $name_for = ''; ?>
						<?php for ($i=0; $i < count($emp); $i++) { 
							if ($emp[$i]->employee_id == $confirm->order_by) {
								$name_by = $emp[$i]->name;
							}
							if ($emp[$i]->employee_id == $confirm->order_for) {
								$name_for = $emp[$i]->name;
							}
						} ?>
						<td>{{$name_by}}</td>
						<td>{{$confirm->due_date}}</td>
						<td>{{$confirm->order_for}}</td>
						<td>{{$name_for}}</td>
						<td>
						<select class="form-control select2" id="status_{{$confirm->id}}" style="width: 100%;" onchange="statusChange(value)">
							<option value="{{$confirm->id}}_Approved" selected="selected">Approve</option>
							<option value="{{$confirm->id}}_Rejected">Reject</option>
						</select>
						</td>
					</tr>
					<?php $index++; ?>
					@endforeach
				</tbody>
			</table>
			<button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 2vw;" onclick="confirmOrder()" id="btnConfirm">
				CONFIRM ORDER
			</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			minimumResultsForSearch: -1
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function statusChange(val){
		str = val.split('_');

		if(str[1] == 'Approved'){
			$('#list_'+str[0]).css('background-color', '#ccff90');
			$('#list_'+str[0]).css('color', '#000');
		}
		else{
			$('#list_'+str[0]).css('background-color', '#ff6090');
			$('#list_'+str[0]).css('color', '#fff');
		}
	}

	function confirmOrder(){
		if(confirm("Are you sure want to make this order?")){
			$('#loading').show();
			var rejected = [];
			var approved = [];
			$('#tableConfirm tbody tr').each(function() {
				str = this.id.split('_');
				status = $('#status_'+str[1]).val();
				str2 = status.split('_');
				if(str2[1] == 'Rejected'){
					rejected.push(parseInt(str2[0]));
				}
				else{
					approved.push(parseInt(str2[0]));
				}
			});

			var data = {
				approved:approved,
				rejected:rejected,
			}

			$.post('{{ url("approve/ga_control/live_cooking") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					location.reload();
					openSuccessGritter('Success!', result.message);
					$('#loading').hide();
					return false;
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
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