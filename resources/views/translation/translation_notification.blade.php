@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		padding:  2px 5px 2px 5px;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
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
		@if($code == 1)
		<div class="col-lg-6">
			<div id="container1"></div>
		</div>
		<div class="col-lg-6">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td style="width: 30%; font-weight: bold;">ID</td>
						<td style="width: 70%;">{{ $translation->translation_id }}</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">Pemohon</td>
						<td style="width: 70%;">{{ $translation->requester_id }} - {{ $translation->requester_name }}</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">Department</td>
						<td style="width: 70%;">{{ $translation->department_name }}</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">Permintaan Selesai</td>
						<td style="width: 70%;">{{ date('d F Y' ,strtotime($translation->request_date)) }}</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">Estimasi Jumlah Halaman</td>
						<td style="width: 70%;">{{ $translation->number_page }} Halaman ({{ $translation->document_type }})</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">Estimasi Waktu</td>
						<td style="width: 70%;">{{ $translation->load_time }} Menit</td>
					</tr>
					<tr>
						<td style="width: 30%; font-weight: bold;">PIC</td>
						<td style="width: 70%;" id="pic">{{ $translation->pic_id }} - {{ $translation->pic_name }}</td>
					</tr>
				</tbody>
			</table>
			<div id="assignment">
				<form role="form">
					<div class="form-group">
						<label style="padding-top: 0;" for="translationText">Pilih PIC<span class="text-red">*</span> :</label>
						<select class="form-control select2" id="createPIC" data-placeholder="Select PIC" style="width: 100%;">
						</select>
					</div>
				</form>
				<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 100%;" onclick="selectPIC()">CONFIRM</button>
			</div>
			<div id="assignment_success" style="display: none;">
				<p style="font-weight: bold; font-size: 1.2vw; color: green;">ASSIGNMENT SUCCESS !!</p>
			</div>
		</div>
		@endif
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/data.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchLoad();
		$('#createPIC').select2({
			minimumResultsForSearch: -1
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function selectPIC(){
		var pic = $('#createPIC').val().split('||');
		var pic_id = pic[0];
		var pic_name = pic[1];
		var translation_id = "{{ $translation->translation_id }}";

		var data = {
			pic_id:pic_id,
			pic_name:pic_name,
			translation_id:translation_id
		}
		$.post('{{ url("input/translation_pic") }}', data, function(result, status, xhr){
			if(result.status){
				$('#assignment').hide();
				$('#pic').text(result.pic_id+' - '+result.pic_name);
				$('#assignment_success').show();
				fetchLoad();
				$('#loading').hide();
				openSuccessGritter('Success!',data.message);
				audio_ok.play();	
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',data.message);
				audio_error.play();				
			}
		});
	}

	function fetchLoad(){
		var data = {

		}
		$.get('{{ url("fetch/translation_load") }}', data, function(result, status, xhr){
			if(result.status){
				var xCategories = [];
				var series_meeting = [];
				var series_translation = [];
				$('#createPIC').html("");
				var createPIC = "";
				createPIC += '<option value=""></option>';

				$.each(result.loads, function(key, value){
					xCategories.push(value.pic_name);
					series_meeting.push(value.load_time_meeting);
					series_translation.push(value.load_time_translation);
					createPIC += '<option value="'+value.pic_id+'||'+value.pic_name+'">'+value.pic_id+' - '+value.pic_name+' (Load: '+value.load_time_total+' Min)</option>';
				});

				$('#createPIC').append(createPIC);

				Highcharts.chart('container1', {
					chart: {
						backgroundColor: null,
						type: 'column'
					},
					title: {
						text: 'Interpreter Work Load'
					},
					xAxis: {
						categories: xCategories
					},
					credits: {
						enabled: false
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Minute(s)'
						},
						stackLabels: {
							enabled: true
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 25,
						floating: true,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || null,
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121',
							dataLabels: {
								enabled: true
							}
						}
					},
					series: [{
						name: 'Meeting',
						data: series_meeting,
						color: '#90ed7d'
					}, {
						name: 'Translation',
						data: series_translation,
						color: '#605ca8'
					}]
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
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
