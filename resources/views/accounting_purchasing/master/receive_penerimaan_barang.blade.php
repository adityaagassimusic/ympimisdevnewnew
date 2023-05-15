@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading { display: none; }

	#qr_apar {
		text-align: center;
		font-weight: bold;
	}
	div.dataTables_wrapper div.dataTables_filter label{
	  color: white;
	}

	.alert {
    /*width: 50px;
    height: 50px;*/
    -webkit-animation: alert 1s infinite;  /* Safari 4+ */
    -moz-animation: alert 1s infinite;  /* Fx 5+ */
    -o-animation: alert 1s infinite;  /* Opera 12+ */
    animation: alert 1s infinite;  /* IE 10+, Fx 29+ */
}

	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 20px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

@-webkit-keyframes alert {
	0%, 49% {
		/*background: rgba(0, 0, 0, 0);*/
		background: #ccffff; 
		/*opacity: 0;*/
	}
	50%, 100% {
		background-color: #f55359;
	}
}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered" id="tableList" style="width: 100%; margin-bottom: 0px">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="7">Detail Pengiriman Barang</th>
					</tr>
					<tr>
						<th width="2%">No</th>
						<th width="5%">Nomor PO</th>
						<th width="20%">Deskripsi Item</th>
						<th width="5%">Jumlah</th>
						<th width="5%">Tanggal Diterima</th>
						<th width="5%">Penerima</th>
						<th width="15%">Cek</th>
					</tr>
				</thead>
				<tbody id="body_check_list">

				</tbody>
			</table>
		</div>

		 <div class="col-xs-4" style="padding-right: 0; padding-left: 0; margin-bottom: 2%; border-radius: 5px" id="this_area">
            <div class="input-group input-group-lg" style="border: 1px solid black;">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
                <i class="fa fa-credit-card"></i>
              </div>
              <input type="text" class="form-control" placeholder="TAP ID CARD HERE" id="item_scan">
            </div>
            <input type="hidden" id="code">
        </div>
        <div class="col-xs-8" style="margin-bottom: 2%; border-radius: 5px">
        	<div class="input-group input-group-lg" style="border: 1px solid black;">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
                <i class="fa fa-users"></i>
              </div>
              <input type="text" id="op_all" class="form-control" placeholder="Detail Penerima Karyawan" readonly>
                <input type="hidden" id="op_id" class="form-control" placeholder="Employee ID" readonly>
                <input type="hidden" id="op_name" class="form-control" placeholder="Employee Name" readonly>
         	 </div>
        </div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;" id="check">
			<button class="btn btn-lg btn-success" style="width: 100%" id="btn-check"><i class="fa fa-check"></i> Check</button>
		</div>

	</section>

	@endsection
	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/jsQR.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
    		$("#item_scan").focus();
			getList();
		});


		$('#item_scan').keydown(function(event) {
		  if (event.keyCode == 13 || event.keyCode == 9) {
		    if ($("#item_scan").val() != "") {
		      checkCode($("#item_scan").val());
		    } else {
		      $("#op_all").val("");
		      $("#op_id").val("");
		      $("#op_name").val("");
		      audio_error.play();
		      openErrorGritter('Error', 'Invalid CARD');
		    }
		  }
		});

		function checkCode(param) {
		  var data = {
		    tag : param
		  }

		  $.get('{{ url("scan/warehouse/penerimaan_barang") }}', data, function(result, status, xhr){
		    if (result.status) {
		      $("#op_all").val(result.datas.employee_id+" - "+result.datas.name);
		      $("#op_id").val(result.datas.employee_id);
		      $("#op_name").val(result.datas.name);
		      audio_ok.play();
		      openSuccessGritter('Success', '');
		    } else {
		      audio_error.play();
		      openErrorGritter('Error', result.message);
		    }

		    $('#item_scan').val("");
		  })
		}

		function getList() {

			var data = {
				id: "{{$id}}"
			}

			$.get('{{ url("fetch/warehouse/penerimaan_barang") }}',data, function(result, status, xhr) {
				
				t_body = "";
				$("#body_check_list").empty();

				var no = 1;

				$.each(result.received, function(index, value){
					var cek = "";
					var color = "";
					if (value.pic_date_receive != null) {
          	var tanggal_fix = value.pic_date_receive.replace(/-/g,'/');						
					}
					else{
						var tanggal_fix = 0
					}


					if (value.pic_receive == null) {
						color = "#f15c80";
					}
					else{
						color = "green";
					}
					t_body += "<tr>";
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+no+"</td>";
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+value.no_po+"</td>";
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: left; color: white;'>&nbsp;&nbsp;"+value.nama_item+"</td>"
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+value.qty_receive+"</td>";
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+getFormattedDate(new Date(value.date_receive))+"</td>";
					t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+(value.penerima || '-')+"</td>";
					if (value.pic_receive == null) {
						t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: #000000; font-size: 20px;'><div class='checkbox' style='margin-top:5px;margin-bottom:5px'>";
						t_body += "<label><input type='checkbox' class='check checkmark' name='"+value.id+"'>OK</label></div></td>";
					}
					else{
						t_body += "<td style='padding:0;background-color: "+color+"; text-align: center; color: white;'>"+value.pic_receive+" <br> Pada "+getFormattedTime(new Date(tanggal_fix))+"</td>";
					}
					t_body += "</tr>";

					no++;
				});

				$("#body_check_list").append(t_body);

				var table = $('#tableList').DataTable({
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
			        ]
			      },
			      'paging': false,
			      'lengthChange': true,
			      'searching': true,
			      'ordering': false,
			      'info': false,
			      'autoWidth': true,
			      "sPaginationType": "full_numbers",
			      "bJQueryUI": true,
			      "bAutoWidth": false,
			      "processing": true,
			      "aaSorting": [[ 0, "desc" ]]
			    });
			})
		}


		$("#btn-check").click(function() {
    
		    var employee_id = $("#op_id").val();
		    var name = $("#op_name").val();

			var check_list = [];
			$(".check").each(function( i ) {
				if ($(this).is(':checked')) {
					check_list.push($(this).attr("name"));
				} else {
					// ng_list.push($(this).attr("name"));
					// check_list.push(0);
				}
			});

			if(check_list.length == 0){
				openErrorGritter("Error", "Pastikan Item Dipilih");
				return false;
			}

			if(employee_id == null || employee_id == ""){
				openErrorGritter("Error", "Pastikan User Sudah Diisi");
				return false;
			}

			// console.log(check_list);
			// return false;

			$("#loading").show();

			var data = {
				check : check_list,
			    employee_id : $("#op_id").val(),
			    name : $("#op_name").val(),
			}
			$.post('{{ url("post/warehouse/penerimaan_barang") }}', data, function(result, status, xhr) {
				if (result.status) {
					$("#loading").hide();
					
					getList();
			        
	        $("#op_all").val("");					
	        $("#op_id").val("");
	        $("#op_name").val("");
					$("#item_scan").focus();

					$(".check").prop('checked', false).parent().removeClass('active');
					openSuccessGritter('Success', 'Check Berhasil Ditambahkan');
					// console.log(hasil_check);
					location.reload();
				} else {
					$("#loading").hide();
					openErrorGritter("Error", "Cek Koneksi Wifi Anda");
				}
			})
		})


		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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

		function getFormattedTime(date) {
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
			  
			  return day + '-' + monthNames[month] + '-' + year +' '+ hour +':'+ minute +':'+ second;
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
			});
		}

	</script>
	@endsection