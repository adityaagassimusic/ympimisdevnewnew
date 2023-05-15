@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#listTableBodyOutstanding > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
	}
	.container {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.container input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.container .checkmark:after {
	  left: 10px;
	  top: 5px;
	  width: 5px;
	  height: 12px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">

		<?php if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'STD')) { ?>

		<li><a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Tambah Data Kecelakaan Kerja</a></li>

		<?php } ?> 
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
						<div class="col-xs-12" style="background-color:  #fbdd0b ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;color:white" align="center">
							<span style="font-size: 25px;color: black;width: 25%;">REKAP KECELAKAAN KERJA YAMAHA GROUP</span>
							<span style="font-size: 25px;color: black;width: 25%;"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
	    	<!-- <div class="col-md-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
	    		<div id="chart2" style="width: 100%"></div>
	    	</div> -->
	    	<div id="container" style="height: 50vh;"></div>
	    </div>
	</div>
	<div class="box">
		<div class="box-body">
			<center><h4 id="title_proses" style="font-weight: bold"></h4></center>
	    	<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Tanggal Kejadian</th>
						<th>Lokasi</th>
						<th>Kondisi Korban</th>
						<th>Ilustrasi</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog modal-lg" style="width: 900px">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;background-color: #fbdd0b;color: black;" id="modalNewTitle"></h3></center>
					<div class="row">
						<input type="hidden" id="id_edit">
						<input type="hidden" name="lop" id="lop" value="1">

						<div class="col-md-1">
						</div>
						<div class="col-md-10">
							<div class="col-md-6" style="margin-bottom: 5px;padding: 0;">
								<label for="submission_date" class="col-sm-12 control-label">Tanggal Pembuatan<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<div class="input-group date">
										<div class="input-group-addon">	
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
										<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
									</div>
								</div>
							</div>

							<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
								<label for="title" class="col-sm-12 control-label">Pembuat Laporan<span class="text-red">*</span></label>
								<div class="col-sm-12">
									@if($employee != null)
									<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
									<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
									<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
									<input type="hidden" id="emp_department" name="emp_department" value="{{$employee->department}}">
									@else
									<input type="text" class="form-control" value=" - " readonly="">
									<input type="hidden" id="emp_id" name="emp_id" value="">
									<input type="hidden" id="emp_name" name="emp_name" value="">
									<input type="hidden" id="emp_department" name="emp_department" value="">
									@endif
								</div>
							</div>	

							<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
								<label for="location" class="col-sm-12 control-label">Lokasi<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<select class="form-control select3" id="location" name="location" data-placeholder='Lokasi' style="width: 100%">
										<option value="">&nbsp;</option>
										@foreach($location as $loc)
										<option value="{{$loc}}">{{$loc}}</option>
										@endforeach
									</select>
								</div>
							</div>											

							<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
								<label for="area" class="col-sm-12 control-label">Area<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="area" name="area" placeholder="Area Kejadian">
								</div>
							</div>

							<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
								<label for="submission_date" class="col-sm-12 control-label">Tanggal Kejadian<span class="text-red">*</span></label>
								<div class="col-sm-8" style="padding-right:0">
									<div class="input-group date">
										<div class="input-group-addon">	
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right datepicker" placeholder="Tanggal Kejadian" id="date_incident" name="date_incident">
									</div>
								</div>
								<div class="col-sm-4">
									<input type="text" id="time_incident" class="form-control timepicker" value="12:00">
								</div>
								
							</div>

							<div class="col-md-6" style="margin-bottom: 5px;padding: 0">
								<label for="position" class="col-sm-12 control-label">Korban<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="position" name="position" placeholder="Korban">
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px;padding: 0">
								<label for="detail_incident" class="col-sm-12 control-label">Kronologi Kejadian<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<textarea type="text" class="form-control" id="detail_incident" name="detail_incident" placeholder="Detail Kejadian"></textarea>
								</div>
							</div>

							<div class="col-md-12" style="margin-bottom: 5px;padding: 0">
								<label for="condition" class="col-sm-12 control-label">Kondisi Korban<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="condition" name="condition" placeholder="Kondisi Korban"> 
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px;padding: 0">

								<label for="loss_time" class="col-sm-12 control-label">Waktu Kerja Hilang (Hari)</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="loss_time" name="loss_time" placeholder="E.g. : 2 Hari"> 
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px;padding: 0">

								<label for="recovery_time" class="col-sm-12 control-label">Perkiraan Sembuh (Hari)</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="recovery_time" name="recovery_time" placeholder="E.g. : 1 Hari"> 
								</div>
							</div>

							<div class="col-md-4" style="margin-bottom: 5px;padding: 0">

								<label for="loss_cost" class="col-sm-12 control-label">Kerugian ($ Dollar)</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="loss_cost" name="loss_cost" placeholder="E.g. : $ 60">
								</div>
							</div>

							<div class="col-md-5 ilustrasi" style="margin-bottom: 5px;padding: 0">

								<label for="illustration_image" class="col-sm-12 control-label">Foto kejadian</label>
								<div class="col-sm-12">
									<input type="file" class="form-control" id="illustration_image1" name="illustration_image1"> 
								</div>
							</div>

							<div class="col-md-5 ilustrasi" style="margin-bottom: 5px;padding: 0">

								<label for="illustration_detail" class="col-sm-12 control-label">Detail Foto Kejadian</label>
								<div class="col-sm-12">
									<input type="text" class="form-control" id="illustration_detail1" name="illustration_detail1" placeholder="Detail Ilustrasi"> 
								</div>
							</div>

							<div class="col-md-2 ilustrasi" style="margin-bottom: 5px;padding: 0">

								<label for="action" class="col-sm-12 control-label">Action</label>
								<div class="col-sm-12">
									<button class="btn btn-success" type="button" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
								</div>
							</div>

								<div id="illustration_edit"></div>

							<div id="tambah"></div>

							<div class="col-md-12" style="margin-bottom: 5px;padding: 0">
								<label for="yokotenkai" class="col-sm-12 control-label">Konten Yokotenkai<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<textarea type="text" class="form-control" id="yokotenkai" name="yokotenkai" placeholder="Konten Yokotenkai"></textarea>
								</div>
							</div>

						</div>
					</div>

					<div class="col-md-1">
					</div>

					<div class="col-md-12" style="padding-top:20px">
						<a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="data-activity">
         	<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
		        <thead>
			        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
				        <th style="padding: 5px;text-align: center;width: 1%">Tanggal</th>
				        <th style="padding: 5px;text-align: center;width: 1%">Lokasi</th>
				        <th style="padding: 5px;text-align: center;width: 2%">Area</th>
				        <th style="padding: 5px;text-align: center;width: 10%">Detail Kejadian</th>
				        <th style="padding: 5px;text-align: center;width: 3%">Kondisi Korban</th>
			        </tr>
		        </thead>
		        <tbody id="bodyTableDetail">
		        	
		        </tbody>
		    </table>
          </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="modalChart" style="color: black;z-index: 10000;">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				
        <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_chart"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
					<div class="col-xs-12">
						<div id="container_sosialisasi" style="height: 40vh;"></div>
					</div> 

					<div class="col-xs-12">
						<input type="hidden" id="id_sosialiasi" name="id_sosialiasi">
						<a type="button" class="btn btn-info" style="width:100%" onclick="sosialiasi_kec()"><i class="fa fa-info-circle"></i> Scan Tap RFID</a>
					</div>

					<div class="col-xs-12" style="margin-top:20px">
						<h4 class="modal-title" id="modalDetailTitleChart"></h4>
						<table class="table table-hover table-bordered table-striped" id="tableDetailChart">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="width: 1%;">#</th>
									<th style="width: 3%;">Employee ID</th>
									<th style="width: 9%;">Name</th>
									<th style="width: 9%;">Dept</th>
									<th style="width: 3%;">Status</th>
									<th style="width: 3%;">At</th>
								</tr>
							</thead>
							<tbody id="tableDetailChartBody">
							</tbody>
						</table> 
					</div>
        </div>
      </div>
	  </div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


    // var no = 0;
	var no = 2;

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
		fetchChart();
	});

	CKEDITOR.replace('yokotenkai' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '250px'
    });


	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$('.select3').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $("#modalNew")
	});




	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){

		if(id == 'new'){
			$('#modalNewTitle').text('Data Kecelakaan Kerja');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newButton').hide();
			$('.ilustrasi').hide();
			$('#updateButton').show();
			
			var data = {
				id:id
			}

			$.get('{{ url("detail/kecelakaan") }}', data, function(result, status, xhr){
				if(result.status){


					$('#id_edit').val(result.accident.id);

					$('#location').html('');
					var location = "";


          $.each(result.location, function(key, value){
              if(value == result.accident.location){
                  location += '<option value="'+value+'" selected>'+value+'</option>';
              }
              else{
                  location += '<option value="'+value+'">'+value+'</option>';
              }
          });

					$('#location').append(location);

					$('#submission_date').val(result.accident.submission_date);
					$('#emp_id').val(result.accident.employee_id);
					$('#emp_name').val(result.accident.employee_name);
					$('#emp_department').val(result.accident.employee_department);
					$('#position').val(result.accident.position);
					$('#location').val(result.accident.location);
					$('#area').val(result.accident.area);
					$('#date_incident').val(result.accident.date_incident);
					$('#time_incident').val(result.accident.time_incident);
					$('#detail_incident').val(result.accident.detail_incident);
					$('#condition').val(result.accident.condition);
					$('#loss_time').val(result.accident.loss_time);
					$('#recovery_time').val(result.accident.recovery_time);
					$('#loss_cost').val(result.accident.loss_cost);

					var data_illustrasi_image = JSON.parse(result.accident.illustration_image);
					var data_illustrasi_detail = JSON.parse(result.accident.illustration_detail);


					$('#illustration_edit').html("");
					var isi_ilustrasi = "";

                    for (var i = 0; i < data_illustrasi_image.length; i++) { 
						isi_ilustrasi += '<div class="col-md-12" style="padding:0"><div class="col-md-5 ilustrasi" style="margin-bottom: 5px;padding: 0"><label for="illustration_image" class="col-sm-12 control-label">Foto kejadian</label><div class="col-sm-12"><img src="{{url("files/kecelakaan/kecelakaan_kerja")}}/'+data_illustrasi_image[i]+'" width="200"> </div></div><div class="col-md-7 ilustrasi" style="margin-bottom: 5px;padding: 0"><label for="illustration_detail'+i+'" class="col-sm-12 control-label">Detail Foto Kejadian</label><div class="col-sm-12"><input type="text" class="form-control" id="illustration_detail'+i+'" name="illustration_detail'+i+'" value="'+data_illustrasi_detail[i]+'" placeholder="Detail Ilustrasi"> </div></div>';
					}

					// <div class="col-md-2" style="margin-bottom: 5px;padding: 0"><label for="action" class="col-sm-12 control-label">Action</label><div class="col-sm-12"><button class="btn btn-success" type="button" onclick="tambah(\''+id+'\',\''+lop+'\');"><i class="fa fa-plus"></i></button></div></div></div>

					$('#illustration_edit').append(isi_ilustrasi);
        			$("#yokotenkai").html(CKEDITOR.instances.yokotenkai.setData(result.accident.yokotenkai));

					$('#modalNewTitle').text('Update Data Kecelakaan Kerja');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

	function Save(id){	

		$('#loading').show();

		if(id == 'new'){
			if($("#submission_date").val() == "" || $('#position').val() == "" || $('#location').val() == "" || $('#area').val() == "" || $('#date_incident').val() == "" || $('#time_incident').val() == "" || $('#detail_incident').val() == "" || $('#condition').val() == "" || CKEDITOR.instances.yokotenkai.getData() == "") {
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			
			formData.append('lop', $("#lop").val());
			formData.append('submission_date', $("#submission_date").val());
			formData.append('emp_id', $("#emp_id").val());
			formData.append('emp_name', $("#emp_name").val());
			formData.append('emp_department', $("#emp_department").val());
			formData.append('position', $("#position").val());
			formData.append('location', $("#location").val());
			formData.append('area', $("#area").val());
			formData.append('date_incident', $("#date_incident").val());
			formData.append('time_incident', $("#time_incident").val());
			formData.append('detail_incident', $("#detail_incident").val());
			formData.append('condition', $("#condition").val());
			formData.append('loss_time', $("#loss_time").val());
			formData.append('recovery_time', $("#recovery_time").val());
			formData.append('loss_cost', $("#loss_cost").val());
			for(var i = 1; i <= $('#lop').val(); i++){
	          	formData.append('illustration_image'+i, $('#illustration_image'+i).prop('files')[0]);
				formData.append('illustration_detail'+i, $('#illustration_detail'+i).val());
	        }
			formData.append('yokotenkai', CKEDITOR.instances.yokotenkai.getData());

			$.ajax({
				url:"{{ url('create/kecelakaan/kerja') }}",
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
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
						fetchChart();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#submission_date").val() == "" || $('#position').val() == "" || $('#location').val() == "" || $('#area').val() == "" || $('#date_incident').val() == "" || $('#time_incident').val() == "" || $('#detail_incident').val() == "" || $('#condition').val() == "" || CKEDITOR.instances.yokotenkai.getData() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign");
				return false;
			}
			var formData = new FormData();
			
			formData.append('id_edit', $("#id_edit").val());
			formData.append('submission_date', $("#submission_date").val());
			formData.append('emp_id', $("#emp_id").val());
			formData.append('emp_name', $("#emp_name").val());
			formData.append('emp_department', $("#emp_department").val());
			formData.append('position', $("#position").val());
			formData.append('location', $("#location").val());
			formData.append('area', $("#area").val());
			formData.append('date_incident', $("#date_incident").val());
			formData.append('time_incident', $("#time_incident").val());
			formData.append('detail_incident', $("#detail_incident").val());
			formData.append('condition', $("#condition").val());
			formData.append('loss_time', $("#loss_time").val());
			formData.append('recovery_time', $("#recovery_time").val());
			formData.append('loss_cost', $("#loss_cost").val());
          	// formData.append('illustration_image', $("#illustration_image").prop('files')[0]);
			// formData.append('illustration_detail', $("#illustration_detail").val());
			formData.append('yokotenkai', CKEDITOR.instances.yokotenkai.getData());

			$.ajax({
				url:"{{ url('edit/kecelakaan') }}",
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
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}
				}
			});
		}
	}

	function clearNew(){
		$('#id_edit').val('');
		$('#title').val('');
		$('#currency').val('').trigger('change');
		$("#amount").val('');
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

	function getMonthName(date) {
	  var year = date.getFullYear();

    var monthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];

    var month = date.getMonth();

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    
    return monthNames[month];
	}


	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/kecelakaan/kerja") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.accident, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.date_incident))+' '+value.time_incident+'</td>';
					listTableBody += '<td style="width:2%;">'+value.location+' - '+value.area+'</td>';
					listTableBody += '<td style="width:2%;">'+value.condition+'</td>';

					if (value.illustration_image != null) {
						var data = JSON.parse(value.illustration_image);
						listTableBody += '<td style="width:0.1%;">'

	                    for (var i = 0; i < data.length; i++) { 
							listTableBody += '<a target="_blank" href="{{ url("files/kecelakaan/kecelakaan_kerja") }}/'+data[i]+'"><i class="fa fa-paperclip"></i>';
						}


						listTableBody += '</td>'
					}
					else{
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					if (value.status == null && value.status_foreman == null) {
						listTableBody += '<td style="width:2%;"><center>';

						if ("{{Auth::user()->role_code}}" == 'S-MIS' || "{{Auth::user()->role_code}}" == 'S-STD' || "{{Auth::user()->role_code}}" == 'C-STD') {

						listTableBody += '<button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button> ';

						}

						 listTableBody += '<a class="btn btn-md btn-danger" target="_blank" href="{{ url("index/kecelakaan/report") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> | <button class="btn btn-md btn-success" onclick="sendEmail(\''+value.id+'\')" title="Send Email To All Employee"><i class="fa fa-envelope"></i> - <i class="fa fa-users"></i></button> | <button class="btn btn-md btn-warning" onclick="sendEmailForeman(\''+value.id+'\')" title="Send Email To All Foreman"><i class="fa fa-envelope"></i> - <i class="fa fa-user"></i> </button> <a class="btn btn-md btn-info" onclick="ShowChart(\''+value.id+'\')"><i class="fa fa-bar-chart"></i> </a></center>';
						 listTableBody += '</td>';
					}
					else if (value.status == null) {
						listTableBody += '<td style="width:2%;"><center>';


						if ("{{Auth::user()->role_code}}" == 'S-MIS' || "{{Auth::user()->role_code}}" == 'S-STD' || "{{Auth::user()->role_code}}" == 'C-STD') {

						listTableBody += '<button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button> ';

						}

						listTableBody += '<a class="btn btn-md btn-danger" target="_blank" href="{{ url("index/kecelakaan/report") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> | <button class="btn btn-md btn-success" onclick="sendEmail(\''+value.id+'\')" title="Send Email To All Employee"><i class="fa fa-envelope"></i> - <i class="fa fa-users"></i></button> | <span class="label label-success"> Sent</span> <a class="btn btn-md btn-info" onclick="ShowChart(\''+value.id+'\')"><i class="fa fa-bar-chart"></i> </a></center>';
						listTableBody += '</td>';

					}
					else if (value.status_foreman == null) {
						listTableBody += '<td style="width:2%;"><center>';


						if ("{{Auth::user()->role_code}}" == 'S-MIS' || "{{Auth::user()->role_code}}" == 'S-STD' || "{{Auth::user()->role_code}}" == 'C-STD') {

						listTableBody += '<button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button> ';

					 }

						listTableBody += '<a class="btn btn-md btn-danger" target="_blank" href="{{ url("index/kecelakaan/report") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> | <span class="label label-success"> Sent</span> | <button class="btn btn-md btn-warning" onclick="sendEmailForeman(\''+value.id+'\')" title="Send Email To All Foreman"><i class="fa fa-envelope"></i> - <i class="fa fa-user"></i> </center></button>';
						listTableBody += '</td>';
					}
					else {

						listTableBody += '<td style="width:2%;"><center><a class="btn btn-md btn-danger" target="_blank" href="{{ url("index/kecelakaan/report") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <a class="btn btn-md btn-info" onclick="ShowChart(\''+value.id+'\')"><i class="fa fa-bar-chart"></i> </a> <a class="btn btn-md btn-primary" target="_blank" href="{{ url("index/yokotenkai") }}/'+value.id+'"><i class="fa fa-paste"></i> Yokotenkai </a></center></td></button>';
					}

				

					listTableBody += '</tr>';

					count_all += 1;
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
					'searching': true,
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

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

// 	function drawChart() {
// 	$.get('{{ url("fetch/monitoring/kecelakaan/kerja") }}', function(result, status, xhr) {
// 		if(xhr.status == 200){
// 			if(result.status){
// 				var bulan = [], jumlah = [];

// 				$.each(result.datas, function(key, value) {
// 					bulan.push(value.bulan);
// 					jumlah.push(parseInt(value.jumlah));
// 				});

// 				var date = new Date();

// 				$('#chart2').highcharts({
// 					chart: {
// 						type: 'column',
// 						height : '250px'
// 					},
// 					title: {
// 						text: ''
// 					},
// 					credits : {
// 						enabled:false
// 					},
// 					xAxis: {
// 						type: 'category',
// 						categories: bulan
// 					},
// 					yAxis: {
// 						min: 0,
// 						title: {
// 							text: 'Total Data Per Bulan'
// 						},
// 						stackLabels: {
// 							enabled: true,
// 							style: {
// 								fontWeight: 'bold',
//                         color: ( 
//                         	Highcharts.defaultOptions.title.style &&
//                         	Highcharts.defaultOptions.title.style.color
//                         	) || 'gray'
//                       }
//                     },
//                   	tickInterval: 1
//                   },

//                   legend: {
//                   	align: 'right',
//                   	x: -30,
//                   	verticalAlign: 'top',
//                   	y: 25,
//                   	floating: true,
//                   	backgroundColor:
//                   	Highcharts.defaultOptions.legend.backgroundColor || 'white',
//                   	borderColor: '#CCC',
//                   	borderWidth: 1,
//                   	shadow: false,
//                   	enabled:false
//                   },
//                   tooltip: {
//                   	headerFormat: '<b>{point.x}</b><br/>',
//                   	pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
//                   },
//                   plotOptions: {
//                   	column: {
//                   		stacking: 'normal',
//                   		dataLabels: {
//                   			enabled: true
//                   		}
//                   	}
//                   },
//                   series: [{
//                   	name: 'Jumlah',
//                   	data: jumlah,
//                   	color: '#ff9800'
//                   }]
//                 })
// 			} else{
// 				alert('Attempt to retrieve data failed');
// 			}
// 		}
// 	})
// }


var detail_modal;
	function fetchChart(){
			$.get('{{ url("fetch/monitoring/kecelakaan/kerja") }}',function(result, status, xhr){
				if(result.status){

					var month = [], 
					tahun = [],
					total_data = [];

					detail_modal = [];

					$.each(result.datas, function(key, value){
						month.push(value.bulan);
			            tahun.push(value.tahun);
			            total_data.push({y: parseInt(value.jumlah),key:value.tahun});
					});


					$.each(result.detail, function(key2, value2){
						detail_modal.push({
							date_incident:value2.date_incident,
							location:value2.location,
							area:value2.area,
							detail_incident:value2.detail_incident,
							condition:value2.condition
						});
					});

					const chart = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container',
					        type: 'column',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        }
					    },
					    xAxis: {
							categories: month,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
				                formatter: function (e) {
				                  return ''+ this.value +' '+tahun[(this.pos)];
				                }
				              },
						},
						yAxis: [{
							title: {
								text: 'Total Data',
								style: {
									color: '#eee',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"15px"
								}
							},
							type: 'linear',
							opposite: true
						}
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							enabled: false,
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
								fontSize:'12px',
							}
						},	
					    title: {
					        text: ''
					    },
					    subtitle: {
					        text: ''
					    },
					    plotOptions: {
					        column: {
					            // stacking: 'normal'
					        },
					        series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModal(this.category,this.series.name,this.options.key);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [
					    {
			                name: 'Jumlah Kasus',
			                data: total_data,
			                color : '#f0ad4e'
			            }]
					});

				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			});
		}

	function ShowModal(bulan,status,tahun) {
			$('#tableDetail').DataTable().clear();
      		$('#tableDetail').DataTable().destroy();

			$('#loading').show();
			$('#bodyTableDetail').html('');
			var tableDetail = '';

			for(var i = 0; i < detail_modal.length;i++){


				if (getMonthName(new Date(detail_modal[i].date_incident)) === bulan && new Date(detail_modal[i].date_incident).getFullYear() == tahun) {

					tableDetail += '<tr>';
					tableDetail += '<td style="width:1%">'+getFormattedDate(new Date(detail_modal[i].date_incident))+'</td>';
					tableDetail += '<td style="width:2%">'+detail_modal[i].location+'</td>';
					tableDetail += '<td style="width:2%">'+detail_modal[i].area+'</td>';
					tableDetail += '<td style="width:10%">'+detail_modal[i].detail_incident+'</td>';
					tableDetail += '<td style="width:1%">'+(detail_modal[i].condition || '')+'</td>';
					tableDetail += '</tr>';
				}
			}
			$('#bodyTableDetail').append(tableDetail);
			$('#tableDetail').DataTable({
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
              'searching': true,
              'ordering': true,
              'order': [],
              'info': true,
              'autoWidth': true,
              "sPaginationType": "full_numbers",
              "bJQueryUI": true,
              "bAutoWidth": false,
              "processing": true
            });

            $('#judul_detail').html('Detail '+status+' Pada Bulan '+bulan+' '+tahun);
			$('#modalDetail').modal('show');
			$('#loading').hide();
		}

	function tambah(id,lop) {
		var id = id;
		var lop = "";
		if (id == "tambah"){
			lop = "lop";
		}else{
			lop = "lop2";
		}
		var divdata = $("<input type='text' name='lop' id='lop' value='"+no+"' hidden><div id='"+no+"' class='col-md-12' style='padding: 0; padding-top: 5px'><div class='col-md-5' style='margin-bottom: 5px;padding: 0'><label for='illustration_image"+no+"' class='col-sm-12 control-label'>Foto kejadian</label><div class='col-sm-12'><input type='file' class='form-control' id='illustration_image"+no+"' name='illustration_image"+no+"'> </textarea></div></div><div class='col-md-5' style='margin-bottom: 5px;padding: 0'><label for='illustration_detail"+no+"' class='col-sm-12 control-label'>Detail Foto Kejadian</label><div class='col-sm-12'><input type='text' class='form-control' id='illustration_detail"+no+"' name='illustration_detail"+no+"' placeholder='Detail Ilustrasi'> </textarea></div></div><div class='col-xs-2' style='padding:0; padding-left: 5px'><label for='action' class='col-sm-12 control-label'>&nbsp;</label><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i></button>&nbsp;<button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");
		
		$("#"+id).append(divdata);
		$('#lop').val(no);
		$('.select3').select2();

		no+=1;
	}

	function kurang(elem,lop) {
		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#illustration_image"+newid).attr("name","illustration_image"+oldid);
		jQuery("#illustration_detail"+newid).attr("id","illustration_detail"+oldid);
		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) { 
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#illustration_image"+newid).attr("name","illustration_image"+oldid);
			jQuery("#illustration_detail"+newid).attr("id","illustration_detail"+oldid);
		}
		document.getElementById(lop).value = a;
	}

	function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Berita Ini Ke Semua Member yang memiliki email?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("index/kecelakaan/sendemail/kerja") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim Ke Seluruh Karyawan");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function sendEmailForeman(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Berita Ini Ke Semua Chief dan Foreman Untuk Konten Yokotenkai?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("index/kecelakaan/sendemail/foreman") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim Ke Seluruh Karyawan");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function sosialiasi_kec(){
    	var id = $("#id_sosialiasi").val();
    	window.location.href = '{{url("index/kecelakaan/sosialisasi")}}/'+id;
    }

    function ShowChart(id){

			$('#loading').show();

			$('#modalDetailTitleChart').html('');
			$('#tableDetailChart').hide();
			$('#tableDetailChart').DataTable().clear();
			$('#tableDetailChart').DataTable().destroy();
			$('#tableDetailChartBody').html('');

			var data = {
				id:id
			}

			$.get('{{ url("chart/kecelakaan") }}',data,function(result, status, xhr){
				if(result.status){

					$("#id_sosialiasi").val(id);
					$('#loading').hide();
					xCategories = [];
					belum = [];
					sudah = [];

					var total = 0;
					var total_belum = 0;
					var total_sudah = 0;

					mcu_detail = [];
					periode = '';

					for(var i = 0; i < result.department.length;i++){

						var count_sudah = 0;
						var count_belum = 0;
						var sosil = [];
						for(var j = 0; j < result.sosialisasi.length; j++){
							sosil.push(result.sosialisasi[j].employee_id);
						}

							for(var k = 0; k < result.employees.length;k++){
								if (sosil.includes(result.employees[k].employee_id) && result.employees[k].department == result.department[i].department_name) {
										count_sudah++;
										total_sudah++;
										mcu_detail.push({
											employee_id:result.employees[k].employee_id,
											name:result.employees[k].name,
											department_shortname:result.department[i].department_shortname,
											department:result.department[i].department,
											section:result.employees[k].section,
											group:result.employees[k].group,
											sub_group:result.employees[k].sub_group,
											status_cek:'Sudah',
										});
								}else if(!sosil.includes(result.employees[k].employee_id) && result.employees[k].department == result.department[i].department_name){
									count_belum++;
									total_belum++;
									mcu_detail.push({
										employee_id:result.employees[k].employee_id,
										name:result.employees[k].name,
										department_shortname:result.department[i].department_shortname,
										department:result.department[i].department,
										section:result.employees[k].section,
										group:result.employees[k].group,
										sub_group:result.employees[k].sub_group,
										status_cek:'Belum',
									});
								}
							}
						sudah.push({y:parseInt(count_sudah),key:result.department[i].department_name});
						belum.push({y:parseInt(count_belum),key:result.department[i].department_name});
						xCategories.push(result.department[i].department_shortname);
					}

					const chart = new Highcharts.Chart({
					    chart: {
					        renderTo: 'container_sosialisasi',
					        type: 'column',
					        options3d: {
					            enabled: true,
					            alpha: 0,
					            beta: 0,
					            depth: 50,
					            viewDistance: 25
					        }
					    },
					    xAxis: {
							categories: xCategories,
							type: 'category',
							gridLineWidth: 0,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:1,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Data',
								style: {
									color: '#eee',
									fontSize: '15px',
									fontWeight: 'bold',
									fill: '#6d869f'
								}
							},
							labels:{
								style:{
									fontSize:"15px"
								}
							},
							type: 'linear',
							opposite: true
						}
						],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							itemStyle: {
								fontSize:'12px',
							},
							reversed : true
						},	
					    title: {
					        text: ''
					    },
					    subtitle: {
					        text: ''
					    },
					    plotOptions: {
					        series:{
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											ShowModalDetailChart(this.options.key,this.series.name,id);
										}
									}
								},
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								cursor: 'pointer',
								depth:25
							},
					    },
					    credits:{
					    	enabled:false
					    },
					    series: [{
							type: 'column',
							data: belum,
							name: 'Belum Sosialisasi',
							colorByPoint: false,
							color:'#f44336'
						},{
							type: 'column',
							data: sudah,
							name: 'Sudah Sosialisasi',
							colorByPoint: false,
							color:'#32a852'
						}
						]
					});
				}


				$('#judul_chart').html('Detail Sosialiasi Kecelakaan Kerja '+result.accident.condition);
				$('#modalChart').modal('show');
			})
		}

		function ShowModalDetailChart(dept,stat,id) {
		$('#tableDetailChart').hide();
		var data = {
			dept:dept,
			stat:stat,
			id:id,
		}

		$.get('{{ url("chart/kecelakaan/detail") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableDetailChartBody').html('');

				$('#tableDetailChart').DataTable().clear();
				$('#tableDetailChart').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.details, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '<td>'+ value.stat +'</td>';
					resultData += '<td>'+ value.attend_time +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailChartBody').append(resultData);
				$('#modalDetailTitleChart').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Attendance "+stat+" in "+dept+"</span></center>");

				$('#tableDetailChart').show();
				var table = $('#tableDetailChart').DataTable({
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

