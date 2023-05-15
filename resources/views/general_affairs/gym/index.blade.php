@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>td{
		text-align:center;
	}
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
	table.table-bordered > tfoot > tr > td{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	.blink_text {

		animation:1.2s blinker linear infinite;
		-webkit-animation:1.2s blinker linear infinite;
		-moz-animation:1.2s blinker linear infinite;

		color: yellow;
	}

	@-moz-keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@-webkit-keyframes blinker {
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}
	#loading, #error { display: none; }
	.fc-event {
		font-size: 1vw;
		cursor: pointer;
	}

	.fc-event-time, .fc-event-title {
		padding: 0 1px;
		white-space: nowrap;
	}

	.fc-title {
		white-space: normal;
	}
	.fc-content {
	    cursor: pointer;
	}
	.content{
		padding-top: 0px;
		padding-left: 7px;
		padding-right: 7px;
		padding-bottom: 0px;
	}
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
		<div class="col-xs-12" style="padding-top: 0px;padding-bottom: 10px">
			<a href="{{url('home')}}" class="btn btn-danger">
				<i class="fa fa-arrow-left"></i> Back
			</a>
			<button class="btn btn-primary" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);" onclick="fetchOrderList()">
				<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
			</button>
			<a class="btn btn-success" href="{{url('index/ga_control/gym/attendance')}}" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);">
				<i class="fa fa-book"></i>&nbsp;&nbsp;Attendance & Tutorial
			</a>
			<a class="btn btn-danger" href="{{url('index/ga_control/gym/regulation')}}" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);">
				<i class="fa fa-info-circle"></i>&nbsp;&nbsp;Regulation
			</a>
			<?php if(ISSET($emp)){
				if (str_contains($role,'GA') || str_contains($role,'MIS')) { ?>
					<a class="btn btn-info" href="{{url('index/ga_control/gym/schedule')}}" style="font-weight: bold;height:35px;color: white;border:1px solid rgb(126,86,134);">
						<i class="fa fa-gear"></i>&nbsp;&nbsp;Setting Schedule
					</a>
				<?php }
			} ?>
		</div>
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<center>
						<span style="background-color: #d2ff8a; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Terdaftar&nbsp;&nbsp;</span>
						<span style="background-color: #96b8ff; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Hadir&nbsp;&nbsp;</span>
						<span style="background-color: black; color: white; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Batal&nbsp;&nbsp;</span>
					</center>
				</div>
				<div class="box-body">
					<div id="calendar"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">BUAT PENDAFTARAN</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" name="addId" id="addId">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">ID<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input class="form-control" type="text" id="addUser" value="<?php if(ISSET($emp)){
										echo $emp->employee_id;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nama<span class="text-red"> :</span></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="addUserName" value="<?php if(ISSET($emp)){
										echo $emp->name;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kategori<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="addCategory" placeholder="Select Date" readonly="">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tanggal<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="addDate" placeholder="Select Date" readonly="">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Jam<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="addStartTime" readonly="">
								</div>
								<div class="col-sm-4" style="padding-left: 0px;">
									<input type="text" class="form-control" id="addEndTime" readonly="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-xs-6" style="padding-left: 0px;">
						<button class="btn btn-danger pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="$('#modalCreate').modal('hide')">BATAL</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px;">
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="confirmOrder()">DAFTAR</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">PEMBATALAN</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" name="editId" id="editId">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">ID<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input class="form-control" type="text" id="editUser" value="<?php if(ISSET($emp)){
										echo $emp->employee_id;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nama<span class="text-red"> :</span></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="editUserName" value="<?php if(ISSET($emp)){
										echo $emp->name;
									} ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tanggal<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="editDate" placeholder="Select Date" readonly="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-xs-6" style="padding-left: 0px;">
						<button class="btn btn-danger pull-right" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="$('#modalEdit').modal('hide')">CLOSE</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px;">
						<button class="btn btn-success pull-right" id="btnCancel" style="font-weight: bold; font-size: 1.5vw; width: 100%;" onclick="confirmCancel()">PEMABATALAN</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


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
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("bower_components/moment/moment.js")}}"></script>
<script src="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		var date = new Date();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: date
		});

		$('#datefrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#dateto').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		// $('.datepicker').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });

		$('#menuDateRandom').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		
		$('.selectEditEmployee').select2({
			dropdownParent:$('#modalDetail')
		});

		fetchOrderList();
	});

	function fetchOrderList(){
		$('#loading').show();
		$.get('{{ url("fetch/ga_control/gym") }}', function(result, status, xhr){
			if(result.status){
				var quota = [];
				var ordered = [];
				var cat = [];

				$.each(result.quota, function(key, value){
					quota.push(value.quota);
					ordered.push(value.order);
					cat.push(value.date);
				});

				var cal = {};
				var cals = [];

				for(var i = 0; i < result.calendars.length;i++){
					if(result.calendars[i].remark == 'H'){
						cal = {
							title: 'Libur 休日',
							quota:0,
							order:0,
							id_gym: "",
							start: Date.parse(result.calendars[i].date),
							allDay: true,
							backgroundColor: '#ff1744',
							textColor: 'white',
							borderColor: 'black',
						}
						cals.push(cal);	
					}else{
						for(var j = 0; j < result.quota.length;j++){
							if (result.quota[j].dates == result.calendars[i].date) {
								var gender = 'All';
								var bgcolor = '#e6e6e6';
								var colors = '';
								if (result.quota[j].gender == 'P') {
									gender = 'Women Only';
									bgcolor = '#FFC0CB';
								}
								cal = {
									title: gender+" ("+result.quota[j].start_time.split(':')[0]+':'+result.quota[j].start_time.split(':')[1]+"-"+result.quota[j].end_time.split(':')[0]+':'+result.quota[j].end_time.split(':')[1]+") ("+result.quota[j].order+"/"+result.quota[j].capacity+")",
									quota:result.quota[j].capacity,
									order:result.quota[j].order,
									id_gym: result.quota[j].id,
									start: Date.parse(result.calendars[i].date),
									allDay: true,
									backgroundColor:  bgcolor,
									textColor: 'black',
									borderColor: 'black'
								}
								cals.push(cal);

								for(var k = 0; k < result.resumes.length;k++){
									if (result.resumes[k].schedule_id == result.quota[j].id) {
										if (result.resumes[k].remark == 'Canceled') {
											cal = {
												title: result.resumes[k].name,
												quota:0,
												order:0,
												id_gym: result.resumes[k].id_gym,
												start: Date.parse(result.resumes[k].date),
												allDay: true,
												backgroundColor: 'black',
												textColor: 'white',
												borderColor: 'black'
											}
											cals.push(cal);
										}else if(result.resumes[k].remark == 'Attended'){
											cal = {
												title: result.resumes[k].name,
												quota:0,
												order:0,
												id_gym: result.resumes[k].id_gym,
												start: Date.parse(result.resumes[k].date),
												allDay: true,
												backgroundColor: '#96b8ff',
												textColor: 'black',
												borderColor: 'black'
											}
											cals.push(cal);
										}else{
											cal = {
												title: result.resumes[k].name,
												quota:0,
												order:0,
												id_gym: result.resumes[k].id_gym,
												start: Date.parse(result.resumes[k].date),
												allDay: true,
												backgroundColor: '#d2ff8a',
												textColor: 'black',
												borderColor: 'black'
											}
											cals.push(cal);
										}
									}
								}
							}
						}
					}
				}

				$(function () {			
					$('#calendar').fullCalendar({
						contentHeight: 600,
						header    : {
							left  : 'prev,next today',
							center: 'title',
							right : 'month,agendaWeek,agendaDay',
						},
						buttonText: {
							today: 'today',
							month: 'month',
							week : 'week',
							day  : 'day'
						},
						eventOrder: 'color,start',
						// dayClick: function(date, allDay, jsEvent, view) { 
						// 	var d = addZero(formatDate(date));
						// 	var event_id = "";
						// 	var event_color = "";
						// 	var id_gym = "";
						// 	$('#calendar').fullCalendar('clientEvents', function(event) {
				  //               if (d == addZero(formatDate(event.start))) {
				  //               	event_id = event.title;
				  //               	event_color = event.backgroundColor;
				  //               }
				  //           });
						// 	openModalCreate('new', d, event_id,event_color,"");
						// },
						eventClick: function(info) {
							if (info.title.match(/All/gi) || info.title.match(/Women/gi)) {
								openModalCreate('new',formatDate(info.start), info.title,info.backgroundColor,info.id_gym,info.quota,info.order);
							}else{
								openModalCreate('detail',formatDate(info.start), info.title,info.backgroundColor,info.id_gym,info.quota,info.order);
							}
						},
						events    : cals,
						editable  : false
					})
					$('#calendar').fullCalendar( 'removeEvents' );
					$('#calendar').fullCalendar( 'addEventSource', cals); 

					var currColor = '#3c8dbc'
					var colorChooser = $('#color-chooser-btn')
					$('#color-chooser > li > a').click(function (e) {
						e.preventDefault()
						currColor = $(this).css('color')
						$('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
					})
					$('#add-new-event').click(function (e) {
						e.preventDefault()
						var val = $('#new-event').val()
						if (val.length == 0) {
							return
						}

						var event = $('<div />')
						event.css({
							'background-color': currColor,
							'border-color'    : currColor,
							'color'           : '#fff'
						}).addClass('external-event')
						event.html(val)
						$('#external-events').prepend(event)

						init_events(event)

						$('#new-event').val('')
					})
				});

				$('#loading').hide();
			}
			else{
				alert('Unidentified Error');
				audio_error.play();
				return false;
			}
		});
	}

	function openModalCreate(cat,date,title,bg,id,quota,order) {
		if (title == 'Libur 休日' || title == '') {
			audio_error.play();
			openErrorGritter('Error!','Tidak Ada Schedule.');
		}else{
			if (cat == 'new') {
				var category = title.split(' ')[0];
				var gender = "LP";
				if (category == 'Women') {
					gender = 'P';
				}
				var genders = '<?php if(ISSET($emp)){
										echo $emp->gender;
									} ?>';
				var re = new RegExp(genders, 'g');
				if (date > '{{date("Y-m-d")}}' && parseInt(order) < parseInt(quota) && gender.match(re)) {
					$("#addId").val(id);
					$("#addDate").val(date);
					var start_time = title.split(' ')[2].split('-')[0].replace('(', "");
					var end_time = title.split(' ')[2].split('-')[1].replace(')', "");
					$('#addStartTime').val(start_time);
					$('#addEndTime').val(end_time);
					$('#addCategory').val(category);
					$('#modalCreate').modal('show');
				}
			}else{
				$('#btnCancel').show();
				$('#loading').show();
				var data = {
					id:id
				}
				$.get('{{ url("edit/ga_control/gym") }}', data,function(result, status, xhr){
					if(result.status){
						if (result.gym.employee_id == '<?php if(ISSET($emp)){
										echo $emp->employee_id;
									} ?>') {
							$("#editId").val(id);
							$("#editDate").val(result.gym.date);
							$('#editUser').val(result.gym.employee_id);
							$('#editUserName').val(result.gym.name);
							if (result.gym.remark == 'Canceled') {
								$('#btnCancel').hide();
							}
							$('#loading').hide();
							$('#modalEdit').modal('show');
						}else{
							$('#loading').hide();
						}
					}else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}
	}

	function confirmOrder() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var id = $('#addId').val();
			var date = $('#addDate').val();
			var ordered_by_id = $('#addUser').val();
			var ordered_by_name = $('#addUserName').val();

			var data = {
				id:id,
				date:date,
				ordered_by_id:ordered_by_id,
				ordered_by_name:ordered_by_name,
			}

			$.post('{{ url("input/ga_control/gym") }}', data,function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Buat Pendaftaran');
					$('#modalCreate').modal('hide');
					fetchOrderList();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function confirmCancel() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var id = $('#editId').val();
			
			var data = {
				id:id,
			}

			$.get('{{ url("delete/ga_control/gym") }}', data,function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Membatalkan');
					$('#modalEdit').modal('hide');
					fetchOrderList();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function formatDate(date) {
		var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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