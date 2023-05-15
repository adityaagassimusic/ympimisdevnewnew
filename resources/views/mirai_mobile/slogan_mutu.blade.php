@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		/*text-align:center;*/
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.buttonclass {
		top: 0;
		left: 0;
		transition: all 0.15s linear 0s;
		position: relative;
		display: inline-block;
		padding: 15px 25px;
		background-color: #ffe800;
		text-transform: uppercase;
		color: #404040;
		font-family: arial;
		letter-spacing: 1px;
		box-shadow: -6px 6px 0 #404040;
		text-decoration: none;
		cursor: pointer;
	}
	.buttonclass:hover {
		top: 3px;
		left: -3px;
		box-shadow: -3px 3px 0 #404040;
		color: white
	}
	.buttonclass:hover::after {
		top: 1px;
		left: -2px;
		width: 4px;
		height: 4px;
	}
	.buttonclass:hover::before {
		bottom: -2px;
		right: 1px;
		width: 4px;
		height: 4px;
	}
	.buttonclass::after {
		transition: all 0.15s linear 0s;
		content: "";
		position: absolute;
		top: 2px;
		left: -4px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg);
		z-index: -1;
	}
	.buttonclass::before {
		transition: all 0.15s linear 0s !important;
		content: "";
		position: absolute;
		bottom: -4px;
		right: 2px;
		width: 8px;
		height: 8px;
		background-color: #404040;
		transform: rotate(45deg) !important;
		z-index: -1 !important;
	}

	a.buttonclass {
		position: relative;
	}

	a:active.buttonclass {
		top: 6px;
		left: -6px;
		box-shadow: none;
	}
	a:active.buttonclass:before {
		bottom: 1px;
		right: 1px;
	}
	a:active.buttonclass:after {
		top: 1px;
		left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small> <span id="periode_fix"></span>
		<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#modalAddAssessor">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;
			Add Assessor (アセッサーを追加)
		</button>
		<button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalUploadSlogan" style="margin-right: 5px;">
			<i class="fa fa-upload"></i>&nbsp;&nbsp;
			Upload Slogan
		</button>
		<!-- <a class="buttonclass">
			button
		</a> -->
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
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
	<div class="alert alert-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4> Warning!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter (フィルター)</h4>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Periode (期間)</span>
							<div class="form-group">
								<select class="form-control select2" name="periode" id="periode" data-placeholder="Pilih Periode (期間を設定する)" style="width: 100%;">
									<option></option>
									@foreach($fy_all as $fy_all)
										<option value="{{$fy_all->fiscal_year}}">{{$fy_all->fiscal_year}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/slogan') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/slogan/report') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
									<button class="btn btn-success col-sm-14" id="btn_seleksi" onclick="saveSelection()">Mulai Proses Seleksi</button>
									<button class="btn btn-success col-sm-14" id="btn_final" onclick="saveFinal()">Mulai Proses Final</button>
									<button class="btn btn-success col-sm-14" id="btn_final_sudah" onclick="saveFinalClose()">Closing Lomba Slogan Mutu</button>
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
						<div class="row" id="divTable">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="background-color: orange;text-align: center;margin-bottom: 10px;">
						<span style="padding: 10px;font-weight: bold;font-size: 20px;">Assessor (アセッサー)</span>
					</div>
					<div class="col-xs-12">
						<div class="row" id="divTableAssessor">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalAddAssessor">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Add Assessor (アセッサーを追加)</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4" align="left">
							<select class="form-control select2" name="periode_asesor" id="periode_asesor" data-placeholder="Pilih Periode" style="width: 100%;">
								<option></option>
								@foreach($fy_all2 as $fy_all2)
									<option value="{{$fy_all2->fiscal_year}}">{{$fy_all2->fiscal_year}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Assessor Seleksi<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<select class="form-control select2" name="select_assessor_seleksi" id="select_assessor_seleksi" data-placeholder="Pilih Nama Assessor Seleksi" style="width: 100%;" multiple="multiple" onchange="changeAssessorSeleksi()">
								<option></option>
								@foreach($emp as $emp)
									<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
								@endforeach
							</select>
							<input type="hidden" id="assessor_seleksi">
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Assessor Final<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<select class="form-control select2" name="select_assessor_final" id="select_assessor_final" data-placeholder="Pilih Nama Assessor Final" style="width: 100%;" multiple="multiple" onchange="changeAssessorFinal()">
								<option></option>
								@foreach($emp2 as $emp2)
									<option value="{{$emp2->employee_id}}_{{$emp2->name}}">{{$emp2->employee_id}} - {{$emp2->name}}</option>
								@endforeach
							</select>
							<input type="hidden" id="assessor_final">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
						<button onclick="addAssessor()" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEditAssessor">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: lightskyblue; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Edit Assessor (アセッサーを編集)</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<input type="hidden" id="id">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4" align="left">
							<select class="form-control select2" name="edit_periode_asesor" id="edit_periode_asesor" data-placeholder="Pilih Periode" style="width: 100%;">
								<option></option>
								@foreach($fy_all3 as $fy_all3)
									<option value="{{$fy_all3->fiscal_year}}">{{$fy_all3->fiscal_year}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Category<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<select class="form-control select2" name="edit_category" id="edit_category" data-placeholder="Pilih Category" style="width: 100%;">
								<option></option>
								<option value="Selection">Selection</option>
								<option value="Final">Final</option>
							</select>
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Assessor<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<select class="form-control select2" name="edit_assessor" id="edit_assessor" data-placeholder="Pilih Nama Assessor" style="width: 100%;">
								<option></option>
								@foreach($emp3 as $emp3)
									<option value="{{$emp3->employee_id}}_{{$emp3->name}}">{{$emp3->employee_id}} - {{$emp3->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
						<button onclick="updateAssessor()" class="btn btn-success pull-right"><i class="fa fa-pencil"></i> Edit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUploadSlogan">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Slogan</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-8">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<input type="file" name="slogan" id="slogan">
						</div>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="form-group row" align="right">
						<div class="col-sm-12" align="left">
							<a class="btn btn-info pull-right" href="{{url('download/slogan')}}">Example</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="uploadSlogan()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
					</div>
				</div>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function changeAssessorSeleksi() {
		$("#assessor_seleksi").val($('#select_assessor_seleksi').val());
	}

	function changeAssessorFinal() {
		$("#assessor_final").val($('#select_assessor_final').val());
	}

	function uploadSlogan() {
		$('#loading').show();
		if($('#slogan').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == ""){
			openErrorGritter('Error!', 'Pilih File Excel.');
			audio_error.play();
			$('#loading').hide();
			return false;	
		}

		var formData = new FormData();
		var fileSlogan  = $('#slogan').prop('files')[0];
		var file = $('#slogan').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('fileSlogan', fileSlogan);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/slogan') }}",
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
					$('#slogan').val("");
					$('#modalUploadSlogan').modal('hide');
					$('#loading').hide();
					fillList();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th style="color:white;border:1px solid black;" width="1%">#</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Periode (期間)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">NIK (従業員ID)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="3%">Nama (名前)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="6%">Slogan (スローガン)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="6%">Seleksi Assessor (選抜 アセッサー)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="3%">Seleksi (選抜)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Seleksi Status (選別状況)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Final Assessor (決勝 アセッサー)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Final (決勝)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Final Result (最終結果)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Final Status (最終状況)</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}

	function initiateTableAssessor() {
		$('#divTableAssessor').html("");
		var tableData = "";
		tableData += "<table id='example2' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th style="color:white;border:1px solid black;" width="1%">#</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Periode (期間)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Category (カテゴリ)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Assessor ID (アセッサーID​​​)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="5%">Assessor Name (アセッサー名)</th>';
		tableData += '<th style="color:white;border:1px solid black;" width="2%">Action (処理)</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example2BodyAssessor">';
		tableData += "</tbody>";
		tableData += "</table>";
		$('#divTableAssessor').append(tableData);
	}

	function fillList(){
		$('#loading').show();
		var data = {
			periode:$('#periode').val()
		}

		$.get('{{ url("fetch/slogan/report") }}',data, function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";

				var selection = '';
				var selection_result = [];
				var selection_status = [];
				var final = '';

				var asesor_seleksi = [];
				var asesor_final = [];
				var asesor_final_sudah = [];

				for(var i = 0; i < result.assessor.length;i++){
					if (result.assessor[i].category == 'Selection') {
						asesor_seleksi.push(result.assessor[i].assessor_id);
					}else{
						asesor_final.push(result.assessor[i].assessor_id);
					}
				}

				var index = 1;
				
				$.each(result.slogan, function(key, value) {
					if (index < 4 && value.selection_status != null && value.final_checks != null) {
						var bgcolor = '#c3e157';
					}else{
						var bgcolor = 'none';
					}
					tableData += '<tr style="background-color:'+bgcolor+'">';
					tableData += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ (key+1) +'</td>';
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.periode +'</td>';
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.employee_id +'</td>';
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.name +'</td>';
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.slogan_1 +'</td>';
					if (value.selection_assessor_id == null) {
						tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;"></td>';
					}else{
						tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.selection_assessor_id+' - '+value.selection_assessor_name+'</td>';
					}
					var color = 'none';
					if (value.selection_checks != null) {
						if (value.selection_checks == 'OK') {
							var color = '#adffad';
						}else{
							var color = '#ffaea8';
						}
					}
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;background-color:'+color+'">';
					tableData += (value.selection_checks || '');
					if (value.nilai != null) {
						tableData += ' ('+value.nilai+')';
					}
					tableData += '</td>'
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (value.selection_status || '') +'</td>';
					if (value.final_checks != null) {
						if (value.final_assessor_id.match(/_/gi)) {
							var final_assessor_id = value.final_assessor_id.split('_');
							var final_assessor_name = value.final_assessor_name.split('_');
							tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">';
							for(var i = 0; i < final_assessor_id.length;i++){
								asesor_final_sudah.push(final_assessor_id[i]);
								tableData += '<span class="label label-success">'+final_assessor_id[i] +' - '+final_assessor_name[i]+'</span><br>';
							}
							tableData += '</td>';
						}else{
							asesor_final_sudah.push(value.final_assessor_id);
							tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;"><span class="label label-success">'+value.final_assessor_id +' - '+value.final_assessor_name+'</span></td>';
						}
					}else{
						tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;"></td>';
					}
					if (value.final_checks != null) {
						if (value.final_checks.match(/_/gi)) {
							var final_checks = value.final_checks.split('_');
							var final_result = value.final_result.split(',');
							tableData += '<td style="border:1px solid black;text-align:right;padding-right:5px;">';
							for(var i = 0; i < final_checks.length;i++){
								if (final_checks[i] == 'NG') {
									var label = 'danger';
								}else{
									var label = 'success';
								}
								tableData += '<span class="label label-'+label+'">'+final_checks[i] +' ('+final_result[i].split('_').map( Number ).reduce((partialSum, a) => partialSum + a, 0)+')</span><br>';
							}
							tableData += '</td>';
						}else{
							if (result.final_checks == 'NG') {
								var label = 'danger';
							}else{
								var label = 'success';
							}
							tableData += '<td style="border:1px solid black;text-align:right;padding-right:5px;"><span class="label label-'+label+'">'+value.final_checks +' ('+value.final_result.split('_').map( Number ).reduce((partialSum, a) => partialSum + a, 0)+')</span></td>';
						}
					}else{
						tableData += '<td style="border:1px solid black;text-align:right;padding-right:5px;"></td>';
					}
					tableData += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ (value.nilai_final || '') +'</td>';
					tableData += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (value.final_status || '') +'</td>';
					tableData += '</tr>';
					selection = (value.selection_assessor_id || '');
					selection_status = (value.selection_status || '');
					if (value.selection_checks != null) {
						selection_result.push('Sudah');
					}else{
						selection_result.push('Belum');
					}
					if (value.final_status != null) {
						final = 'Sudah';
					}
					index++;
				});
				$('#example1Body').append(tableData);

				if (selection == '') {
					$('#btn_seleksi').show();
					$('#btn_final').hide();
				}else{
					$('#btn_seleksi').hide();
					$('#btn_final').hide();
				}

				if (selection_result.join(',').match(/Belum/gi)) {
					$("#btn_final").hide();
				}else{
					if (selection_status != '') {
						$("#btn_final").hide();
					}else{
						$("#btn_final").show();
					}
				}

				var finals = new RegExp(asesor_final.join(','), 'gi');
				$('#btn_final_sudah').hide();
				if (asesor_final_sudah.join(',').match(finals) && final == '') {
					$('#btn_final_sudah').show();
				}

				if (result.slogan.length == 0) {
					$("#btn_final").hide();
					$("#btn_seleksi").hide();
				}

				var table = $('#example1').DataTable({
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

				$('#periode_fix').html('Periode '+result.periode+' <small class="text-purple">'+result.periode+' 期間</small>');

				initiateTableAssessor();

				$('#example2BodyAssessor').html('');
				var tableDataAssessor = '';

				$.each(result.assessor, function(key, value) {
					tableDataAssessor += '<tr>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ (key+1) +'</td>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.periode +'</td>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.category +'</td>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.assessor_id +'</td>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ value.assessor_name +'</td>';
					tableDataAssessor += '<td style="border:1px solid black;text-align:left;padding-left:5px;"><button class="btn btn-warning btn-sm" onclick="editAssessor(\''+value.id+'\',\''+value.periode+'\',\''+value.category+'\',\''+value.assessor_id+'\',\''+value.assessor_name+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteAssessor(\''+value.id+'\')"><i class="fa fa-trash"></i></button></td>';
					tableDataAssessor += '</tr>';
				});

				$('#example2BodyAssessor').append(tableDataAssessor);

				var table = $('#example2').DataTable({
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

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function editAssessor(id,periode,category,assessor_id,assessor_name) {
		$('#edit_periode_asesor').val(periode).trigger('change');
		$('#edit_category').val(category).trigger('change');
		$('#edit_assessor').val(assessor_id+'_'+assessor_name).trigger('change');
		$('#id').val(id);
		$('#modalEditAssessor').modal('show');
	}

	function updateAssessor() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var periode = $('#edit_periode_asesor').val();
			var category = $('#edit_category').val();
			var id = $('#id').val();
			var assessor_id = $('#edit_assessor').val().split('_')[0];
			var assessor_name = $('#edit_assessor').val().split('_')[1];
			var data = {
				id:id,
				periode:periode,
				category:category,
				assessor_id:assessor_id,
				assessor_name:assessor_name,
			}

			$.post('{{ url("update/slogan/assessor") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					$('#modalEditAssessor').modal('hide');
					openSuccessGritter('Success','Sukses Update Assessor');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function deleteAssessor(id) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				id:id,
			}

			$.get('{{ url("delete/slogan/assessor") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					openSuccessGritter('Success','Sukses Delete Assessor');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function saveSelection() {
		if (confirm('Apakah Anda yakin menutup proses Input?')) {
			$('#loading').show();
			var periode = $('#periode').val();
			var data = {
				periode:periode
			}

			$.post('{{ url("input/slogan/report/selection") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					openSuccessGritter('Success','Sukses Mulai Proses Seleksi');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function saveFinal() {
		if (confirm('Apakah Anda yakin menutup Seleksi dan memulai Final?')) {
			$('#loading').show();
			var periode = $('#periode').val();
			var data = {
				periode:periode,
				status:'Start'
			}

			$.post('{{ url("input/slogan/report/final") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					openSuccessGritter('Success','Sukses Mulai Proses Final');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function saveFinalClose() {
		if (confirm('Apakah Anda yakin Closing FInal?')) {
			$('#loading').show();
			var periode = $('#periode').val();
			var data = {
				periode:periode,
				status:'Close'
			}

			$.post('{{ url("input/slogan/report/final") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					openSuccessGritter('Success','Sukses Mulai Proses Final');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
	}

	function addAssessor() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var periode = $('#periode_asesor').val();
			if (periode == '') {
				openErrorGritter('Error!','Pilih Periode');
				$('#loading').hide();
				return false;
			}

			var assessor_seleksi = $('#assessor_seleksi').val();
			var assessor_final = $('#assessor_final').val();

			var data = {
				periode:periode,
				assessor_seleksi:assessor_seleksi,
				assessor_final:assessor_final,
			}

			$.post('{{ url("input/slogan/assessor") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					$('#modalAddAssessor').modal('hide');
					openSuccessGritter('Success','Sukses Input Assessor');
					$('#loading').hide();
				}else{
					alert('Attempt to retrieve data failed');
					$('#loading').hide();
				}
			});
		}
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

	function getFormattedDate(date) {
		var year = date.getFullYear();

		var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
		];

		var month = date.getMonth();

		var day = date.getDate().toString();
		day = day.length > 1 ? day : '0' + day;

		return day + '-' + monthNames[month] + '-' + year;
	}
</script>
@endsection