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
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	th:hover {
		overflow: visible;
	}
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		/*background-color: #FFD700;*/
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<!-- <div class="alert alert-danger alert-dismissible" id="div_check" style="display: none;background-color: rgb(21, 115, 53) !important;border-color: rgb(21, 115, 53) !important">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		<span style="font-size: 20px;font-weight: bold;margin-bottom: 5px">Ada Juri yang belum mengisi penilaian.</span>
		<table class="table table-responsive" id="tableCheck">
			<thead>
				<tr style="background-color: #417dca">
					<th>Employee ID</th>
					<th>Name</th>
					<th>Team No.</th>
					<th>Team Name</th>
				</tr>
			</thead>
			<tbody id="bodyCheck">
				
			</tbody>
		</table>
	</div>	 -->				
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="periode" id="periode" data-placeholder="Pilih Periode" style="width: 100%;">
									<option></option>
									@foreach($periode as $periode)
										<option value="{{$periode->periode}}">{{$periode->periode}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/standardization/ypm') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/standardization/ypm/report') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
									<!-- <button class="btn btn-success col-sm-14" onclick="saveSelection()">Simpan Juara</button> -->
									<button class="btn btn-success col-sm-14" id="btn_approve" onclick="approve()"><i class="fa fa-check"></i> Approve</button>
									<!-- <a class="btn btn-warning col-sm-14" href="" id="print_pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> Print PDF</a> -->
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
						<div class="row">
							<table id="tableYPM" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="" id="headTableYPM">
									
								</thead>
								<tbody id="bodyTableYPM">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
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
	var teams_all = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		// fillList();
		$('#btn_approve').hide();
		$('#bodyTableYPM').html("");
		$('#headTableYPM').html("");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		teams_all = [];
		$('#btn_approve').hide();
		$('#print_pdf').hide();
	});


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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function sortArray(array, property, direction) {
	    direction = direction || 1;
	    array.sort(function compare(a, b) {
	        let comparison = 0;
	        if (a[property] > b[property]) {
	            comparison = 1 * direction;
	        } else if (a[property] < b[property]) {
	            comparison = -1 * direction;
	        }
	        return comparison;
	    });
	    return array; // Chainable
	}

	function fillList(){
		// $('#div_check').hide();
		// $('#print_pdf').hide();
		$('#loading').show();
		if ($('#periode').val() == '') {
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Periode.');
			return false;
		}
		// $('#btn_approve').hide();
		var data = {
			periode:$('#periode').val(),
		}
		$.get('{{ url("fetch/standardization/ypm/report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableYPM').html("");
				$('#headTableYPM').html("");

				var headTableYPM = '';

				// var column = parseInt(result.sga_asesor.length)+2;
				var asesor = [];
				var teams = [];
				var approvals = null;
				for(var i = 0; i < result.report.length;i++){
					if (result.report[i].periode.match(/CONTEST/gi)) {
						asesor.push(result.report[i].asesor_name);
						teams.push(result.report[i].team_id);
					}
					if (result.report[i].std_approval != null) {
						approvals = 'Sudah';
					}
				}
				var judges_approvals = [];
				var asesor_unik = asesor.filter(onlyUnique);
				if (result.judges.length > 0) {
					if (asesor_unik.length == result.judges.length) {
						asesor = [];
						for(var i = 0; i < result.judges.length;i++){
							asesor.push(result.judges[i].judges_name);
							judges_approvals.push(result.judges[i].judges_approval);
						}
					}
				}

				var asesor_unik = asesor.filter(onlyUnique);
				var teams_unik = teams.filter(onlyUnique);

				$('#btn_approve').hide();
				if (approvals == null) {
					$('#btn_approve').show();
				}else{
					if (judges_approvals.join(',').match(/Rejected/gi)) {
						$('#btn_approve').show();
					}
				}

				var tableDataBody = "";
				var index = 1;

				if (asesor_unik.length > 0) {
					headTableYPM += '<tr>';
					headTableYPM += '<th rowspan="2" width="1%" style="background-color: lightskyblue; color: #000;">#</th>';
					headTableYPM += '<th rowspan="2" width="2%" style="background-color: lightskyblue; color: #000;">Team Dept.</th>';
					headTableYPM += '<th rowspan="2" width="2%" style="background-color: lightskyblue; color: #000;">Team ID</th>';
					headTableYPM += '<th rowspan="2" width="3%" style="background-color: lightskyblue; color: #000;">Nama Team</th>';
					headTableYPM += '<th rowspan="2" width="3%" style="background-color: lightskyblue; color: #000;">Title</th>';
					headTableYPM += '<th rowspan="2" width="3%" style="background-color: lightskyblue; color: #000;">Hadiah</th>';
					if (approvals != null) {
						headTableYPM += '<th rowspan="2" width="1%" style="background-color: cornflowerblue; color: #000;" id="approval_std">STD</th>';
					}
					for(var i = 0; i < asesor_unik.length;i++){
						var judges_approval = '';
						var judges_approved_at = '';
						if (result.judges.length > 0) {
							for(var j = 0; j < result.judges.length;j++){
								if (result.judges[j].judges_name == asesor_unik[i]) {
									if (result.judges[j].judges_approval != null) {
										judges_approval = result.judges[j].judges_approval;
										judges_approved_at = result.judges[j].judges_approved_ats;
									}
								}
							}
						}
						if (judges_approval != '') {
							if (judges_approval == 'Approved') {
								headTableYPM += '<th colspan="4" width="1%" style="background-color: #00a65a; color: #fff;text-align:center;">'+asesor_unik[i]+' - '+judges_approval+' - '+judges_approved_at+'</th>';
							}else{
								headTableYPM += '<th colspan="4" width="1%" style="background-color: #f39c12; color: #fff;text-align:center;">'+asesor_unik[i]+' - '+judges_approval+' - '+judges_approved_at+'</th>';
							}
						}else{
							headTableYPM += '<th colspan="4" width="1%" style="background-color: cornflowerblue; color: #000;text-align:center;">'+asesor_unik[i]+'</th>';
						}
					}
					headTableYPM += '<th rowspan="2" width="1%" style="background-color: cornflowerblue; color: #000;">Total Nilai Keseluruhan</th>';
					headTableYPM += '</tr>';

					headTableYPM += '<tr>';
					for(var i = 0; i < asesor_unik.length;i++){
						for(var j = 0; j < result.point.length;j++){
							headTableYPM += '<th width="1%" style="background-color: cornflowerblue; color: #000;">'+result.point[j].criteria.split(' <br><small class="text-purple" style="font-size: 15px;">')[0]+'</th>';
						}
						headTableYPM += '<th width="1%" style="background-color: cornflowerblue; color: #000;">Total Nilai</th>';
					}
					headTableYPM += '</tr>';

					$('#headTableYPM').append(headTableYPM);

					teams_all = [];

					for(var j = 0; j < teams_unik.length;j++){
						var team_dept = '';
						var team_name = '';
						var team_title = '';
						var std_name = '';
						var std_approval = '';
						var std_approved_at = '';
						var hadiah = '';
						$.each(result.report, function(key, value) {
							if (value.periode.match(/CONTEST/gi) && value.team_id == teams_unik[j]) {
								team_dept = value.team_dept;
								team_name = value.team_name;
								team_title = value.team_title;
								if (value.hadiah != null) {
									hadiah = value.hadiah;
								}
							}
							if (value.std_name != null) {
								std_name = value.std_name;
								std_approval = value.std_approval;
								std_approved_at = value.std_approved_ats;
							}
						});
						var total = 0;
						var nilais = [];
						var nilai_all = [];
						for(var i = 0; i < asesor_unik.length;i++){
							var nilai = 0;
							$.each(result.report, function(key, value) {
								if (value.periode.match(/CONTEST/gi) && value.team_id == teams_unik[j] && asesor_unik[i] == value.asesor_name) {
									nilai = nilai + parseInt(value.result);
									nilai_all.push(parseInt(value.result));
								}
							});
							nilai_all.push(nilai);
							nilais.push(nilai);
							total = total + nilai;
						}

						teams_all.push({
							team_dept:team_dept,
							team_id:teams_unik[j],
							team_name:team_name,
							team_title:team_title,
							hadiah:hadiah,
							nilai:nilais,
							nilai_all:nilai_all,
							std_name:std_name,
							std_approval:std_approval,
							std_approved_at:std_approved_at,
							total:parseInt(total)
						});
					}

					if (teams_all.length > 0) {
						teams_all = sortArray(teams_all, "total", -1);

						$('#approval_std').hide();

						for(var j = 0; j < teams_all.length;j++){
							if (j < 6) {
								var bgcolor = 'background-color:lightyellow';
							}else{
								var bgcolor = '';
							}
							tableDataBody += '<tr style="'+bgcolor+'">';
							tableDataBody += '<td style="padding:10px;text-align:right">'+ index +'</td>';
							tableDataBody += '<td style="padding:10px;text-align:left">'+ teams_all[j].team_dept +'</td>';
							tableDataBody += '<td style="padding:10px" id="team_id_'+j+'">'+ teams_all[j].team_id +'</td>';
							tableDataBody += '<td style="padding:10px">'+ teams_all[j].team_name +'</td>';
							tableDataBody += '<td style="padding:10px">'+ teams_all[j].team_title +'</td>';
							if (teams_all[j].hadiah != '' || teams_all[j].hadiah != null) {
								tableDataBody += '<td style="padding:0px;"><input type="text" id="hadiah_'+j+'" onkeyup="checkHadiah(\''+j+'\')" class="form-control" style="width:100%" value="'+teams_all[j].hadiah+'" placeholder="1000000"></td>';
							}else{
								tableDataBody += '<td style="padding:0px;"><input type="text" id="hadiah_'+j+'" onkeyup="checkHadiah(\''+j+'\')" class="form-control" placeholder="1000000" style="width:100%"></td>';
							}
							if (teams_all[j].std_name != '') {
								$('#approval_std').show();
								tableDataBody += '<td style="text-align:left;padding:5px;background-color:#00a65a;color:white;font-size:11px">'+ teams_all[j].std_name +'<br>'+ teams_all[j].std_approval +'<br>'+ teams_all[j].std_approved_at +'</td>';
							}
							var nilai_all = teams_all[j].nilai_all;
							for(var k = 0; k < nilai_all.length;k++){
								tableDataBody += '<td style="text-align:center;font-size:18px;">'+ nilai_all[k] +'</td>';
							}
							// var nilais = teams_all[j].nilai;
							// for(var i = 0; i < nilais.length;i++){
							// 	tableDataBody += '<td style="text-align:right">'+ nilais[i] +'</td>';
							// }
							tableDataBody += '<td style="text-align:right;text-align:center;font-size:18px;">'+ teams_all[j].total +'</td>';
							tableDataBody += '</tr>';
							index++;
						}

						$('#tableYPM').DataTable().clear();
						$('#tableYPM').DataTable().destroy();
						$('#bodyTableYPM').append(tableDataBody);

						var table = $('#tableYPM').DataTable({
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
							"order": [],
							'info': true,
							'autoWidth': true,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
							"bAutoWidth": false,
							"processing": true
						});
					}
				}

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function checkHadiah(index) {
		if ($('#hadiah_'+index).val().length < 7) {
			$('#hadiah_'+index).val($('#hadiah_'+index).val());
		}else{
			$('#hadiah_'+index).val($('#hadiah_'+index).val().slice(0,7));
		}
	}

	function approve() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			if (teams_all.length > 0) {
				var salah = 0;
				for(var i = 0; i < teams_all.length;i++){
					if (i < 6) {
						if ($('#hadiah_'+i).val() == '') {
							salah++;
						}
					}
				}
				if (salah > 0) {
					$('#loading').hide();
					openErrorGritter('Error','Isikan Hadiah');
					return false;
				}
			}
			var hadiah = [];
			var team_id = [];

			for(var i = 0; i < teams_all.length;i++){
				if (i < 6) {
					hadiah.push($('#hadiah_'+i).val());
					team_id.push($('#team_id_'+i).text());
				}
			}

			var data = {
				team_id:team_id,
				hadiah:hadiah,
				periode:$('#periode').val()
			}

			var url = '{{ url("input/standardization/ypm/hadiah") }}';
			$.post(url,data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','Hadiah Berhasil Diinput');
					$('#loading').hide();
					approve_all();
				}else{
					$('#loading').hide();
					openErrorGritter('Error',result.message);
				}
			});
		}
	}

	function approve_all() {
		$('#loading').show();
		var url = '{{ url("approval/standardization/ypm/") }}/'+$('#periode').val()+'/std/PI1910002';
		$.get(url, function(result, status, xhr){
			if(result.status){
				fillList();
				openSuccessGritter('Success','Approved');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error',result.message);
			}
		});
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }



</script>
@endsection