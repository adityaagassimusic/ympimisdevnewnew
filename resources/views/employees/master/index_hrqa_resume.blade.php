@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	h5 {
		margin-bottom: 0px;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
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

	table.table-bordered > tbody > tr:hover{
		background-color: #84f576;
	}

	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
		font-size: 20px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}

	#left {
		height:450px;
		overflow-y: scroll;
	}

	#right {
		height:450px;
		overflow-y: scroll;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}}<span class="text-purple"> </span>
		<small><span class="text-purple"> {{$title_jp}}</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
@php
$avatar = 'images/avatar/'.Auth::user()->avatar;
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-purple" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="period" placeholder="Select Year" style="text-align: center" value="{{ date('Y') }}">
					</div>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success"><i class="fa fa-search"></i> Search</button>
				</div>
				
				<div class="col-xs-12">
					<br>
					<table class="table table-bordered table-stripped" style="width: 100%" id="master_table">
						<thead style="background-color: #e8c2f0">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">January</th>
								<th style="width: 1%">February</th>
								<th style="width: 1%">March</th>
								<th style="width: 1%">May</th>
								<th style="width: 1%">April</th>
								<th style="width: 1%">June</th>
								<th style="width: 1%">July</th>
								<th style="width: 1%">August</th>
								<th style="width: 1%">September</th>
								<th style="width: 1%">October</th>
								<th style="width: 1%">November</th>
								<th style="width: 1%">December</th>
								<th style="width: 1%">Sub Total</th>
							</tr>
						</thead>
						<tbody id="body_table">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		drawData();
	});

	$('#period').datepicker({
		autoclose: true,
		format: "yyyy",
		viewMode: "years", 
		minViewMode: "years"
	});

	function drawData() {
		var data = {
			year : $("#period").val()
		}

		$.get('{{ url("fetch/qnaHR/resume") }}', data, function(result) {
			if (result.status) {
				var body = '';
				var categories = [];
				var mon = [];
				var sums = [];

				var total_sums = 0;

				$("#body_table").empty();

				$.each(result.datas, function(key, value) {
					if(categories.indexOf(value.category) === -1){
						categories[categories.length] = value.category;
					}

					if(mon.indexOf(value.mon) === -1){
						mon[mon.length] = value.mon;
					}
				})

				$.each(categories, function(key2, value2) {
					var sum1 = 0;
					body += '<tr>';
					body += '<td style="background-color: #e8c2f0">'+value2+'</td>';
					$.each(result.datas, function(key, value) {
						if(value2 == value.category) {
							if (value.jmls != 0) {
								body += '<td style="background-color:#c0fa91">'+value.jmls+'</td>';
							} else {
								body += '<td>'+value.jmls+'</td>';
							}

							sum1 += value.jmls;
						}
					});

					body += '<td style="background-color: #c7c7c7">'+sum1+'</td>';
					body += '</tr>';
					total_sums += sum1;
				});

				$.each(mon, function(key2, value2) {
					var sum2 = 0;
					$.each(result.datas, function(key, value) {
						if(value2 == value.mon) {
							sum2 += value.jmls;
							total_sums += sum2;
						}
					})
					sums.push(sum2);
				});

				body += '<tr>';
				body += '<td style="background-color: #e8c2f0">Sub Total</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[0]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[1]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[2]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[3]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[4]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[5]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[6]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[7]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[8]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[9]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[10]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+sums[11]+'</td>';
				body += '<td style="background-color: #c7c7c7">'+total_sums+'</td>';
				body += '</tr>';


				$("#body_table").append(body);
			} else {
				openDangerGritter('Error', result.message);
			}
		})
	}

	function openDangerGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
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

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

</script>
@endsection

