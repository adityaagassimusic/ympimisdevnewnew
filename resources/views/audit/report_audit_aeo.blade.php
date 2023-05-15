@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: white;
		text-align: center;
		vertical-align : middle;
		border: 1px solid black !important;
	}
	table.table-bordered > tbody > tr > td{
	  	color: black;
	  	border: 1px solid black !important;
	}

	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		Report Audit AEO
	</h1>
	<ol class="breadcrumb">
     
     @if(Auth::user()->role_code == "S-MIS" )
     <a class="btn btn-primary btn-sm" style="margin-right: 5px" href="{{ url("/index/audit_aeo/point_check") }}">
       <i class="fa fa-plus"></i>&nbsp;&nbsp;Point & Hasil Check Audit
     </a>
     @endif

     <button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
       <i class="fa fa-edit"></i>&nbsp;&nbsp;Reload Page
     </button>

    </ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
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
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

	<div class="row">
		<div class="col-xs-12">

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">

				<input type="hidden" name="kategori" id="kategori" value="{{ $category }}">
				<input type="hidden" name="lokasi" id="lokasi" value="{{ $location }}">
				<input type="hidden" name="auditor" id="auditor" value="{{ $auditor }}">
				<input type="hidden" name="date" id="date" value="{{ $audit_date }}">
							
				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: white; background-color: #735cdd;">
						<!-- <tr>
							<th colspan="2">PT. Yamaha Musical Products Indonesia</th>
							<th rowspan="2">Tanggal<br> 21 Maret 2019</th>
							<th rowspan="2" colspan="2">Revisi  00</th>
							<th rowspan="2" colspan="2">ISO 45001:2018</th>
						</tr> -->
						<tr>
							<th colspan="5" style="border-right: 1px solid black;background: #735cdd;font-size: 24px;font-weight: bold">
							Laporan Hasil Audit AEO
							</th>
						</tr>

						<tr>
							<th style="background-color: #b3c2f2;color: black">Ref</th>
							<th style="background-color: #b3c2f2;color: black">Requirement & Question</th>
							<th style="border-left: 1px solid black;background-color: #b3c2f2;color: black">Jawaban</th>
							<th style="background-color: #b3c2f2;color: black">Evidence</th>
							<th style="background-color: #b3c2f2;color: black">Action</th>
						</tr>
					</thead>
					<tbody id="body_cek">
						
					</tbody>
				</table>
			</div>
		</div>
	</div>


  <div class="modal fade" id="modalPenanganan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Detail Temuan Audit</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-12">
                <h4>Masih Terpenuhi</h4>
                <select class="form-control select2" id="requirement" data-placeholder="Terpenuhi" style="width: 100%; font-size: 20px;">
									<option></option>
									<option value="Iya">Iya</option>
									<option value="Tidak">Tidak</option>
								</select>

								<h4>Hasil</h4>
                <select class="form-control select2" id="result" data-placeholder="Hasil Audit" style="width: 100%; font-size: 20px;">
									<option></option>
									<option value="Perubahan">Perubahan</option>
									<option value="Penambahan">Penambahan</option>
									<option value="Peningkatan">Peningkatan</option>
								</select>
                
                <h4>Keterangan</h4>
                <textarea class="form-control" name="keterangan" id="keterangan" style="height: 100px;"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_jawaban">
          <button type="button" onclick="update_penanganan()" class="btn btn-success"><i class="fa fa-pencil"></i> Submit Jawaban</button>
        </div>
      </div>
    </div>
  </div>


</section>

@endsection
@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').prop('selectedIndex', 0).change();
		$('.select2').select2({
			minimumResultsForSearch : -1
		});

		$('#modalFirst').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#selectDate').datepicker({
	        format: "yyyy-mm-dd",
	        autoclose: true,
	        todayHighlight: true
	    });

	    cek_report();
	})

	function cek_report() {

		var location = $('#lokasi').val();
		var category = $('#kategori').val();
		var auditor = $('#auditor').val();
		var date = $('#date').val();


		var data = {
			location:location,
			category:category,
			auditor:auditor,
			date:date
		}

		$("#loading").show();

		$.get('{{ url("fetch/audit_aeo/report") }}', data, function(result, status, xhr){
			$("#loading").hide();

			$('#tableResult').DataTable().clear();
		    $('#tableResult').DataTable().destroy();

			var body = "";
		    $('#body_cek').html("");

		    count = 1;
			$.each(result.lists, function(index, value){
				// console.log(count);

				body += "<tr>";
				body += "<td width='5%'>"+value.id+"</td>";
				body += "<td width='30%'><b>"+value.point_judul+"</b><br>"+value.point_question+"</td>";

				if(value.note != null){
					body += "<td width='45%'>"+value.note+"</td>";
				}
				else{
					body += "<td width='45%'>-</td>";
				}
				
				if (value.file != null) {
					body += "<td width='5%'><a href={{url('files/audit_aeo/')}}/"+value.file+"><span class='fa fa-paperclip'></a></td>";					
				}
				else{
					body += "<td width='5%'>Tidak Ada File/Evidence</td>";
				}

				if (value.status_ditangani == null) {
					body += '<td width="15%"><button style="height: 100%;" onclick="penanganan(\''+value.id+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-thumbs-o-up"></i> Penanganan</button></td>';
				}else{
					body += "<td width='15%'>Terpenuhi : "+value.requirement+"<br>Hasil : "+value.result+"<br>Keterangan : "+value.keterangan+"</td>";
				}
			

				body += "</tr>";
				count++;
			})

			// console.log(body);

			$("#body_cek").append(body);

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false,
				sorting: false
			} );
		})
	}


  function penanganan(id) {
    $('#modalPenanganan').modal("show");
    $("#requirement").val("");
    $("#result").val("");
    $("#keterangan").val("");
    $("#id_jawaban").val(id);
  }

  function update_penanganan() {

    if ($("#requirement").val() == "") {
      openErrorGritter("Error","Requirement Harus Diisi");
      return false;
    }

    if ($("#result").val() == "") {
      openErrorGritter("Error","Hasil Harus Diisi");
      return false;
    }

    if ($("#keterangan").val() == "") {
      openErrorGritter("Error","keterangan Harus Diisi");
      return false;
    }


    var formData = new FormData();
    formData.append('id', $("#id_jawaban").val());
    formData.append('requirement', $("#requirement").val());
    formData.append('result', $("#result").val());
    formData.append('keterangan', $("#keterangan").val());

    $.ajax({
      url:"{{ url('post/audit_aeo/jawaban') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        openSuccessGritter("Success","Audit Berhasil Ditangani");
        $('#modalPenanganan').modal("hide");
        cek_report();
      },
      error: function (response) {
        openErrorGritter("Error",result.datas);
        $('#modalPenanganan').modal("hide");
      },
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

</script>
@endsection