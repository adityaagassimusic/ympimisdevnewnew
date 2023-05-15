@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/fixedHeader.dataTables.min.css") }}" rel="stylesheet">
<style type="text/css">
	html {
		transition: color 300ms, background-color 300ms;
	}

	.datepicker table tr td span.focused, .datepicker table tr td span:hover {
		background: #955da8;
	}

	table.table-bordered > tbody > tr:hover {
		cursor: pointer;
		background-color: #212121;
	}

	table.table-bordered > thead > tr > th{
		border:1px solid black;
		color: white;
		font-weight: bold;
		text-align: center;
		background-color: #605ca8;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">

			<div class="col-xs-2">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl_start" placeholder="Date From">
				</div>
			</div>

			<div class="col-xs-2">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl_end" placeholder="Date To">
				</div>
			</div>

			<div class="col-xs-2">
				<button class="btn btn-success" id="btn_search" onclick="loadData()"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="col-xs-12">
				<div id="chart" style="margin-top: 10px"></div>
			</div>
			<div class="col-xs-12">
				<button class="btn btn-primary btn-sm" style="margin-top: 10px" data-toggle="modal" data-target="#modalTemuan"><i class="fa fa-plus"></i> Temuan Baru</button>
			</div>
			<div class="col-xs-12">
				<table class="table table-bordered" id="masterTable">
					<thead>
						<th>No</th>
						<th>Machine ID</th>
						<th>Machine Desc</th>
						<th>Machine Group</th>
						<th>Machine Part</th>
						<th>Finding Date</th>
						<th>User</th>
						<th>Action</th>
					</thead>
					<tbody id="masterBody"></tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalTemuan" tabindex="-1" role="dialog" aria-labelledby="modalTemuanLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTemuanLabel" style="text-align: center;"><b>Tambah Temuan Baru</b></h5>
				</div>
				<form class="form-horizontal" method="POST" enctype="multipart/form-data" id="formTemuan">
					<div class="modal-body">
						<div class="box-body">
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-2 control-label">User<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<select class="form-control select2" name="pic_temuan" id="pic_temuan" data-placeholder="Pilih User" style="width: 100%" required>
										<option value=""></option>
										@foreach($user as $usr)
										<option value="{{ $usr->employee_id }}/{{ $usr->name }}">{{ $usr->employee_id }}/{{ $usr->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Nama Mesin<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<input type="hidden" id="nama_mesin" name="nama_mesin">
									<select class="form-control select2" id="mesin" name="mesin" data-placeholder="Pilih Mesin" style="width: 100%" onchange="get_part(this)" required>
										<option value=""></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Grup Mesin</label>

								<div class="col-sm-10">
									<input type="text" class="form-control" id="mesin_group" name="mesin_group" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Part Mesin<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<select class="form-control select2" id="part_mesin" name="part_mesin" data-placeholder="Pilih Part Mesin" style="width: 100%" required>
										<option value=""></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Tanggal Temuan<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<input type="text" class="form-control" id="tanggal_temuan" name="tanggal_temuan" placeholder="Tanggal Temuan" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Deskripsi Temuan<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<textarea id="deskripsi_temuan" class="form-control" name="deskripsi_temuan" required></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Foto Temuan<span class="text-red">*</span></label>

								<div class="col-sm-10">
									<input type="file" name="foto_temuan" required>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Simpan</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalPenanganan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00a65a">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel" style="font-weight: bold; text-align: center">Finding Detail & Handling</h4>
				</div>
				<div class="modal-body">
					<div class="box-body">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="row">
							<div class="col-md-5">
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="machine">Machine Description : </label>
										<br><span name="machine" id="machine"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="machine_group">Machine Group : </label>
										<br><span name="machine_group" id="machine_group"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="location">Location : </label>
										<br><span name="location" id="location"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="machine_part">Machine Part : </label>
										<br><span name="machine_part" id="machine_part"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="check_date">Check Date : </label>
										<br><span name="check_date" id="check_date"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="check">Checked by : </label>
										<br><span name="check" id="check"> </span>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="desc">Description : </label>
										<br><textarea name="desc" id="desc" class="form-control" readonly> </textarea>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group has-success">
										<label for="image">Picture : </label>
										<br><img src="#" id="image" name="image" style="max-width: 600px">
									</div>
								</div>
							</div>
							<div class="col-md-7">
								<h4>Handling Note<span class="text-red">*</span></h4>
								<textarea class="form-control" required="" name="penanganan" id="penanganan" style="height: 100px;" placeholder="Record Handling Results"></textarea> 
								<h4>Handling Photo<span class="text-red">*</span></h4>
								<input type="file" required="" id="bukti_penanganan" name="bukti_penanganan"> 
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
					<input type="hidden" id="id_penanganan">
					<button type="button" onclick="update_penanganan()" class="btn btn-success"><i class="fa fa-check"></i> Submit Form</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width: 90%">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title" style="text-align: center;"><b>Detail Finding</b></h3>
				</div>
				<div class="modal-body">
					<table class="table" id="tableDetail">
						<thead style="background-color: #605ca8; color: white">
							<tr>
								<th>No</th>
								<th>Finding Date</th>
								<th>Machine Id</th>
								<th>Machine Description</th>
								<th>Machine Group</th>
								<th>Machine Part</th>
								<th>Finding Description</th>
								<th>Finding Photo</th>
								<th>User</th>
								<th>Handling Description</th>
								<th>Handling Photo</th>
								<th>Handling By</th>
							</tr>
						</thead>
						<tbody id="bodyDetail"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.fixedHeader.min.js") }}"></script>
<script src="{{ url("js/dataTables.responsive.min.js") }}"></script>

<!-- <script src="{{ url("js/highcharts-gantt.js")}}"></script> -->
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var mons = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march'];

	var machine_list = <?php echo json_encode($mesin); ?>;
	var mc_data = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$(".select2").select2({
			dropdownParent: $('#modalTemuan'), 
		});

		$('#tanggal_temuan').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		loadData();

		var mc = [];

		$.each(machine_list, function(key, value) {
			if(mc.indexOf(value.machine_name) === -1){
				mc[mc.length] = value.machine_name;
				mc_data.push({'machine_name' : value.machine_name, 'description' : value.description, 'machine_group' : value.machine_group,});
			}
		});

		$("#mesin").empty();
		$("#mesin").append('<option value=""></option>');

		$.each(mc_data, function(key2, value2) {
			$("#mesin").append('<option value="'+value2.machine_name+'">'+value2.machine_name+' - '+value2.description+'</option>');
		})

	})

	function loadData() {
		var data = {
			tgl_start : $("#tgl_start").val(),
			tgl_end : $("#tgl_end").val(),
		}

		$.get('{{ url("fetch/maintenance/pm/finding") }}', data, function(result, status, xhr){
			var body = "";
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();

			$.each(result.details, function(key, value) {
				body += "<tr>";
				body += "<td>"+(key+1)+"</td>";
				body += "<td>"+value.machine_id+"</td>";
				body += "<td>"+value.machine_description+"</td>";
				body += "<td>"+value.machine_group+"</td>";
				body += "<td>"+value.part_machine+"</td>";
				body += "<td>"+value.finding_date+"</td>";
				body += "<td>"+value.pic+"</td>";
				body += "<td>";
				body += "<button class='btn btn-success' onclick='openEdit("+value.id+")'><i class='fa fa-thumbs-o-up'></i> Penanganan</button>";
				body += "</td>";
				body += "</tr>";
			})

			$("#masterBody").append(body);

			$('#masterTable').DataTable({
				'responsive':true,
				'paging': true,
				'lengthChange': false,
				'pageLength': 25,
				'searching': true,
				'ordering': true,
				'order': [],
				'info': false,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});

			var open = [];
			var close = [];
			var categories = [];

			$.each(result.datas, function(key, value) {
				open.push(parseInt(value.sum_open));
				close.push(parseInt(value.sum_close));
				categories.push(value.find_date);
			})

			Highcharts.chart('chart', {

				chart: {
					type: 'column'
				},

				title: {
					text: 'Planned Maintenance Finding'
				},

				xAxis: {
					categories: categories
				},

				yAxis: {
					allowDecimals: false,
					min: 0,
					title: {
						text: 'Number of Finding'
					},
					stackLabels: {
						enabled: true,
						style: {
							fontWeight: 'bold',
							fontSize: '16px',
							color: 'white'
						}
					}
				},

				tooltip: {
					formatter: function () {
						return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': ' + this.y + '<br/>' +
						'Total: ' + this.point.stackTotal;
					}
				},

				plotOptions: {
					column: {
						stacking: 'normal',
						dataLabels: {
							enabled: true
						},
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									modalTampil(this.category, this.series.name);
								}
							}
						}
					}
				},

				credits: {
					enabled: false
				},

				series: [{
					name: 'OPEN',
					data: open,
					color : '#f25d52',
					stack: 'UT'
				}, {
					name: 'CLOSE',
					data: close,
					stack: 'UT',
					color : '#52f29a'
				}, 
			// {
			// 	name: 'Jane',
			// 	data: [2, 5, 6, 2, 1],
			// 	stack: 'female'
			// }, {
			// 	name: 'Janet',
			// 	data: [3, 0, 4, 4, 3],
			// 	stack: 'female'
			// }
			]
		});
		})
	}

	function openEdit(id) {
		$('#modalPenanganan').modal("show");

		var data = {
			id : id,
		}

		$.get('{{ url("fetch/maintenance/pm/finding/byId") }}', data, function(result, status, xhr){
			$("#id_penanganan").val(result.detail.id);
			$("#machine").text(result.detail.machine_description);
			$("#machine_group").text(result.detail.machine_group);
			// $("#location").text(result.detail.location);
			$("#machine_part").text(result.detail.part_machine);
			// $("#machine_substance").text(result.detail.substance);
			$("#check_date").text(result.detail.finding_date);
			$("#check").text(result.detail.pic);
			$("#desc").text(result.detail.finding_description);
			$("#image").attr('src', '{{ url("maintenance/finding/finding") }}/'+result.detail.finding_photo);
		})
	}

	function update_penanganan() {
		if( document.getElementById("bukti_penanganan").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Photo');
			return false;
		}

		if( $("#penanganan").val() == ""){
			openErrorGritter('Error!', 'Please Write Handling Note');
			return false;
		}
		$("#loading").show();

		var formData = new FormData();
		formData.append('id', $("#id_penanganan").val());
		formData.append('penanganan', $("#penanganan").val());
		formData.append('bukti_penanganan', $('#bukti_penanganan').prop('files')[0]);

		$.ajax({
			url:"{{ url('post/maintenance/pm/finding') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				$("#loading").hide();

				openSuccessGritter("Success","Finding Successfully Handled");
				$('#modalPenanganan').modal("hide");
				$("#id_penanganan").val("");
				$("#penanganan").val("");
				$('#bukti_penanganan').val(null);

				loadData();
			},
			error: function (response) {
				$("#loading").hide();

				openErrorGritter("Error", response.message);
				// $('#modalPenanganan').modal("hide");
			},
		});
	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
	});

	$("form#formTemuan").submit(function(e) {
		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("upload/maintenance/pm/finding") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#mesin').prop('selectedIndex', 0).change();
				$("#nama_mesin").val("");
				$("#mesin_group").val("");
				$('#part_mesin').prop('selectedIndex', 0).change();
				$("#deskripsi_temuan").val("");
				$("#pic_temuan").prop('selectedIndex', 0).change();
				$("#foto_temuan").val("");

				$('#modalPenanganan').modal('hide');

				openSuccessGritter('Success', result.message);
				loadData();
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function get_part(elem) {
		var val = $(elem).val();

		$("#mesin_group").val('');
		$("#nama_mesin").val('');
		$.each(mc_data, function(key2, value2) {
			if (value2.machine_name == val) {
				$("#mesin_group").val(value2.machine_group);
				$("#nama_mesin").val(value2.description);
			}
		})

		$("#part_mesin").empty();
		$("#part_mesin").append("<option value=''></option>");
		$.each(machine_list, function(key, value) {
			if (value.machine_name == val) {
				$("#part_mesin").append("<option value='"+value.item_check+"'>"+value.item_check+"</option>");
			}
		})
	}

	function modalTampil(category, name) {
		console.log(category+' '+name);
		$("#modalDetail").modal('show');

		var data = {
			dt : category,
			status : name
		}

		$.get('{{ url("fetch/maintenance/pm/finding/ByChart") }}', data, function(result, status, xhr){
			var body = '';
			$("#bodyDetail").empty();

			$.each(result.details, function(key, value) {
				body += "<tr>";
				body += "<td>"+(key+1)+"</td>";
				body += "<td>"+value.finding_date+"</td>";
				body += "<td>"+value.machine_id+"</td>";
				body += "<td>"+value.machine_description+"</td>";
				body += "<td>"+value.machine_group+"</td>";
				body += "<td>"+value.part_machine+"</td>";
				body += "<td>"+value.finding_description+"</td>";
				body += "<td><img src='{{ url('maintenance/finding/finding') }}/"+value.finding_photo+"' style='max-width:50px'> </td>";
				body += "<td>"+value.pic+"</td>";
				body += "<td>"+(value.handling_description || '')+"</td>";
				body += "<td><img src='{{ url('maintenance/finding/handling') }}/"+value.handling_photo+"' style='max-width:50px'> </td>";
				body += "<td>"+(value.handling_by || '')+"</td>";
				body += "</tr>";
			})

			$("#bodyDetail").append(body);

		})

		$("#bodyDetail").empty();
		// $("#bodyDetail").append();
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

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);

</script>
@endsection