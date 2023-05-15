@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
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
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Point of {{ $activity_name }} - {{ $leader }}
		<a href="{{ url('index/jishu_hozen_point/index/'.$id) }}" style="margin-left: 10px" class="btn btn-primary pull-right">List Mesin</a>
    <a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning pull-right">Kembali</a>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <center style="background-color: lightskyblue;margin-bottom: 10px;">
                <span style="font-weight: bold;font-size: 18px;padding: 10px;">Pilih Mesin (Warna Merah = Belum Diinput di Bulan Ini)</span>
              </center>
            </div>
            <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
              @if(count($jishu_hozen_point) != 0)
                <?php $index = 0; ?>
                @foreach($jishu_hozen_point as $jishu_hozen_point)
                  <div class="col-xs-4" style="margin-bottom: 5px">
                    <button class="btn btn-danger" id="btn_{{$jishu_hozen_point->id}}" style="width: 100%" data-toggle="modal" data-target="#create-modal" onclick="add('{{$jishu_hozen_point->id}}','{{$jishu_hozen_point->nama_pengecekan}}')">{{$jishu_hozen_point->nama_pengecekan}}</button>
                  </div>
                @endforeach
              @endif
            </div>
            <hr style="border: 1px solid black">
            <div class="col-md-12" style="margin-top: 10px;">
              <b><span class="text-red">Pilih Tanggal dan Mesin pada filter di bawah untuk mengurangi waktu loading data.</span></b>
            </div>
            <div class="col-md-4" style="margin-top: 10px;">
              <div class="form-group">
                <div class="input-group date">
                  <div class="input-group-addon bg-white">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker" id="tgl" name="month" placeholder="Select Month" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-md-4" style="margin-top: 10px;">
              <div class="form-group">
                <select class="form-control select2" name="point" id="point" multiple="" style="width: 100%;" data-placeholder="Pilih Point" required onchange="changePoint()">
                  @foreach($jishu_hozen_point2 as $jishu_hozen_point2)
                  <option value="{{ $jishu_hozen_point2->id }}">{{ $jishu_hozen_point2->nama_pengecekan }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="jishu_hozen_points" id="jishu_hozen_points">
              </div>
            </div>
            <div class="col-md-1" style="margin-top: 10px;">
              <button class="btn btn-success" onclick="fillList()">Search</button>
            </div>
            <div class="col-xs-12">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Mesin</th>
                    <th>Tanggal</th>
                    <th>Bulan</th>
                    <th>Foto Aktual</th>
                    <th>PIC</th>
                    <th>Send Status</th>
                    <th>Approval Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="bodyJishuHozen">
                  
                </tbody>
              </table>
            </div>
          </div>
				</div>
			</div>
		</div>
	</div>

  <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body">
          Apakah Anda yakin?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="create-modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background-color: green;color: white">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" align="center"><b>Buat Daily Check Mesin</b></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="row">
              <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <input type="hidden" name="jishu_hozen_point_id" id="jishu_hozen_point_id" class="form-control" value="" readonly required="required" title="">
                <input type="hidden" name="inputdepartment" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
                <input type="hidden" class="form-control" name="date" id="inputdate" placeholder="Masukkan Leader" value="{{ date('Y-m-d') }}" readonly>
                <div class="form-group">
                  <label for="">Mesin</label>
                  <input type="text" class="form-control" name="inputnamapengecekan" id="inputnamapengecekan" placeholder="Masukkan Leader" value="" readonly>
                </div>
                <div class="form-group">
                  <label for="">Month</label>
                  <div class="input-group date">
                    <div class="input-group-addon bg-white">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker" id="inputmonth"name="inputmonth" placeholder="Select Month" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                 <label>Group<span class="text-red">*</span></label>
                    <select class="form-control select2" name="inputsubsection" id="inputsubsection" style="width: 100%;" data-placeholder="Pilih Group..." required>
                      <option value=""></option>
                      @foreach($subsection as $subsection)
                      <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                      @endforeach
                    </select>
                </div>
                <div class="form-group">
                  <label for="">PIC</label>
                    <select class="form-control select2" name="inputpic" id="inputpic" style="width: 100%;" data-placeholder="Pilih PIC..." required>
                      <option value=""></option>
                      @foreach($pic as $pic)
                      <option value="{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                 <div class="form-group">
                  <label for="">Image (Max Width 800) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
                  <textarea name="inputfoto_aktual" id="inputfoto_aktual" class="form-control" rows="2" required="required"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Submit" onclick="create()" class="btn btn-primary">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: orange">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Daily Check Mesin</b></h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <div>
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
              <input type="hidden" name="jishu_hozen_point_id" id="jishu_hozen_point_id" class="form-control" value="" readonly required="required" title="">
              <input type="hidden" name="url" id="url_edit" class="form-control" value="">
              <input type="hidden" name="editdepartment" id="editdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
              <input type="hidden" class="form-control" name="editdate" id="editdate" placeholder="Masukkan Leader" readonly>
              <div class="form-group">
                <label for="">Mesin</label>
                <input type="text" class="form-control" name="editnamapengecekan" id="editnamapengecekan" placeholder="Masukkan Leader" value="" readonly>
              </div>
              <div class="form-group">
                <label for="">Month</label>
                <div class="input-group date">
                  <div class="input-group-addon bg-white">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker" id="editmonth"name="editmonth" placeholder="Select Month" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
               <label>Group<span class="text-red">*</span></label>
                  <select class="form-control select3" name="editsubsection" id="editsubsection" style="width: 100%;" data-placeholder="Pilih Group..." required>
                    <option value=""></option>
                    @foreach($subsection2 as $subsection2)
                    <option value="{{ $subsection2->sub_section_name }}">{{ $subsection2->sub_section_name }}</option>
                    @endforeach
                  </select>
              </div>
              <div class="form-group">
                <label for="">PIC</label>
                  <select class="form-control select3" name="editpic" id="editpic" style="width: 100%;" data-placeholder="Pilih PIC..." required>
                    <option value=""></option>
                    @foreach($pic2 as $pic2)
                    <option value="{{ $pic2->name }}">{{ $pic2->employee_id }} - {{ $pic2->name }}</option>
                    @endforeach
                  </select>
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
               <div class="form-group">
                <label for="">Image (Max Width 800) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
                <textarea name="editfoto_aktual" id="editfoto_aktual" class="form-control" rows="2" required="required"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
    fillList();
    CKEDITOR.replace('inputfoto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editfoto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    
    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
		$('body').toggleClass("sidebar-collapse");
		$('#date').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#inputmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#editmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

    $('.select2').select2({
      dropdownParent: $("#create-modal")
    });

    $('#point').select2({
      allowClear:true
    });

    $('.select3').select2({
      dropdownParent: $("#edit-modal")
    });
	});

  function changePoint() {
    $('#jishu_hozen_points').val($('#point').val());
  }

  function add(jishu_hozen_point_id,nama_pengecekan) {
    $('#jishu_hozen_point_id').val(jishu_hozen_point_id);
    $("#inputnamapengecekan").val(nama_pengecekan);
  }

  function fillList() {
    $('#loading').show();
    var month = $('#tgl').val();
    var jishu_hozen_point = $('#jishu_hozen_points').val();

    // if (jishu_hozen_point == '') {
    //   $('#loading').hide();
    //   openErrorGritter('Error!','Isi Semua Filter');
    //   return false;
    // }

    var data = {
      month:month,
      id:'{{$id}}',
      jishu_hozen_point:jishu_hozen_point
    }

    $.get('{{ url("fetch/jishu_hozen_prod") }}', data, function(result, status, xhr){
      if(result.status){
        $('#example1').DataTable().clear();
        $('#example1').DataTable().destroy();
        $('#bodyJishuHozen').html('');

        var bodyJishuHozen = '';

        for(var i = 0; i < result.point.length;i++){
          $('#btn_'+result.point[i].id).removeClass('btn btn-danger');
          $('#btn_'+result.point[i].id).removeClass('btn btn-success');
          $('#btn_'+result.point[i].id).addClass('btn btn-danger');
          jQuery('#btn_'+result.point[i].id).attr("data-target", '#create-modal');
          jQuery('#btn_'+result.point[i].id).attr("data-toggle", 'modal');
        }

        for(var i = 0; i < result.jishu_hozen.length;i++){
          bodyJishuHozen += '<tr>';
          bodyJishuHozen += '<td>'+result.jishu_hozen[i].nama_pengecekan+'</td>';
          bodyJishuHozen += '<td>'+result.jishu_hozen[i].date+'</td>';
          bodyJishuHozen += '<td>'+result.jishu_hozen[i].month+'</td>';
          bodyJishuHozen += '<td>'+result.jishu_hozen[i].foto_aktual+'</td>';
          bodyJishuHozen += '<td>'+result.jishu_hozen[i].pic+'</td>';

          bodyJishuHozen += '<td>';
          if (result.jishu_hozen[i].send_status == null) {
            bodyJishuHozen += '<label class="label label-danger">Belum Terkirim</label>';
          }else{
            bodyJishuHozen += '<label class="label label-success">Sudah Terkirim</label>';
          }
          bodyJishuHozen += '</td>';

          bodyJishuHozen += '<td>';
          if (result.jishu_hozen[i].approval == null) {
            bodyJishuHozen += '<label class="label label-danger">Not Approved</label>';
          }else{
            bodyJishuHozen += '<label class="label label-success">Approved</label>';
          }
          bodyJishuHozen += '</td>';

          bodyJishuHozen += '<td>';
          var url_cetak = "{{url('index/jishu_hozen/print_jishu_hozen/')}}"+'/'+'{{$id}}'+'/'+result.jishu_hozen[i].id+'/'+result.jishu_hozen[i].month;
          bodyJishuHozen += '<a target="_blank" class="btn btn-info btn-sm" href="'+url_cetak+'"><i class="fa fa-file-pdf-o"></i></a>';
          if ('{{$departments}}' != 'Educational Instrument (EI) Department') {
            if (result.jishu_hozen[i].send_status == null) {
              var url_send = "{{url('index/jishu_hozen/sendemail')}}"+'/'+result.jishu_hozen[i].id+'/'+result.jishu_hozen[i].jishu_hozen_point_id;
              bodyJishuHozen += '<a style="margin-left:5px;" class="btn btn-success btn-sm" href="'+url_send+'"><i class="fa fa-envelope"></i></a>';
            }
          }
          var url_edit = '{{ url("index/jishu_hozen/update") }}';
          var ids = '{{$id}}';
          bodyJishuHozen += '<button style="margin-left:5px;" type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_jishu_hozen(\''+url_edit+'\',\''+ids+'\',\''+result.jishu_hozen[i].jishu_hozen_point_id+'\',\''+result.jishu_hozen[i].id+'\');"><i class="fa fa-edit"></i></button>';

          var url_delete = '{{ url("index/jishu_hozen/destroy") }}';
          bodyJishuHozen += '<a style="margin-left:5px;" href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+url_delete+'\',\''+result.jishu_hozen[i].nama_pengecekan+'\',\''+ids+'\',\''+result.jishu_hozen[i].jishu_hozen_point_id+'\' ,\''+result.jishu_hozen[i].id+'\');"><i class="fa fa-trash"></i></a>';
          bodyJishuHozen += '</td>';
          bodyJishuHozen += '</tr>';

          $('#btn_'+result.jishu_hozen[i].jishu_hozen_point_id).removeClass('btn btn-danger');
          $('#btn_'+result.jishu_hozen[i].jishu_hozen_point_id).addClass('btn btn-success');
          jQuery('#btn_'+result.jishu_hozen[i].jishu_hozen_point_id).removeAttr("data-target");
          jQuery('#btn_'+result.jishu_hozen[i].jishu_hozen_point_id).removeAttr("data-toggle");
        }

        $('#bodyJishuHozen').append(bodyJishuHozen);        
        var table = $('#example1').DataTable({
          "order": [],
          'dom': 'Bfrtip',
          'responsive': true,
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
          }
        });
        $('#loading').hide();
      }else{
        $('#loading').hide();
        openErrorGritter('Error!',result.message);
      }
    });
  }

  function create(){
    var leader = '{{ $leader }}';
    var foreman = '{{ $foreman }}';
    var department = $('#inputdepartment').val();
    var subsection = $('#inputsubsection').val();
    var jishu_hozen_point_id = $('#jishu_hozen_point_id').val();
    var date = $('#inputdate').val();
    var month = $('#inputmonth').val();
    var foto_aktual = CKEDITOR.instances.inputfoto_aktual.getData();
    var pic = $('#inputpic').val();

    var data = {
      department:department,
      subsection:subsection,
      date:date,
      month:month,
      foto_aktual:foto_aktual,
      pic:pic,
      leader:leader,
      foreman:foreman
    }
    
    $.post('{{ url("index/jishu_hozen/store/") }}'+'/'+'{{$id}}'+'/'+jishu_hozen_point_id, data, function(result, status, xhr){
      if(result.status){
        $("#create-modal").modal('hide');
        // $('#example1').DataTable().ajax.reload();
        // $('#example2').DataTable().ajax.reload();
        openSuccessGritter('Success','New Area Check has been created');
        window.location.reload();
      } else {
        audio_error.play();
        openErrorGritter('Error','Create Area Check Failed');
      }
    });
  }

  function edit_jishu_hozen(url,id,jishu_hozen_point_id,jishu_hozen_id) {
      $("#loading").show();
      $.ajax({
          url: "{{ route('jishu_hozen.getjishuhozen') }}?id=" + jishu_hozen_id,
          method: 'GET',
          success: function(data) {
            var json = data;
            // obj = JSON.parse(json);
            var data = data.data;
            $("#url_edit").val(url+'/'+id+'/'+jishu_hozen_point_id+'/'+jishu_hozen_id);
            $("#jishu_hozen_point_id").val(data.jishu_hozen_point_id);
            $("#editnamapengecekan").val(data.nama_pengecekan);
            $("#editdepartment").val(data.department);
            $("#editsubsection").val(data.subsection).trigger('change.select2');
            $("#editdate").val(data.date);
            $("#editmonth").val(data.month);
            $("#editfoto_aktual").html(CKEDITOR.instances.editfoto_aktual.setData(data.foto_aktual));
            $("#editpic").val(data.pic).trigger('change.select2');
            $('#loading').hide();
          }
      });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
      var department = $('#editdepartment').val();
      var subsection = $('#editsubsection').val();
      var month = $('#editmonth').val();
      var foto_aktual = CKEDITOR.instances.editfoto_aktual.getData();
      var pic = $('#editpic').val();
      var url = $('#url_edit').val();

      var data = {
        department:department,
        subsection:subsection,
        month:month,
        foto_aktual:foto_aktual,
        pic:pic,
      }
      
      $.post(url, data, function(result, status, xhr){
        if(result.status){
          $("#edit-modal").modal('hide');
          // $('#example1').DataTable().ajax.reload();
          // $('#example2').DataTable().ajax.reload();
          openSuccessGritter('Success','Area Check has been updated');
          fillList();
        } else {
          audio_error.play();
          openErrorGritter('Error','Update Area Check Failed');
        }
      });
    }

    function deleteConfirmation(url, name,id,jishu_hozen_point_id,jishu_hozen_id) {
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+jishu_hozen_point_id+'/'+jishu_hozen_id);
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
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
@endsection