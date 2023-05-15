@extends('layouts.master')
@section('header')
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
  #loading, #error { display: none; }
</style>
<section class="content-header">
  <h1>
    Buat Audit IK
  </h1>
  <ol class="breadcrumb">
 </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

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


  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <form role="form" id="form_audit_ik" method="post" action="{{url('index/audit_report_activity/store/'.$id)}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Department</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="department" placeholder="Masukkan Department" required value="{{ $departments }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Section <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="section" style="width: 100%;" data-placeholder="Pilih Section" required>
                <option value=""></option>
                @foreach($section as $section)
                <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Group <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group" required>
                <option value=""></option>
                @foreach($subsection as $subsection)
                <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Audit Schedule <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="audit_guidance_id" id="audit_guidance_id" style="width: 100%;" data-placeholder="Pilih Schedule" required onchange="changeGuidance(this.value)">
                <option value=""></option>
                @foreach($guidance as $guidance)
                  <option value="{{ $guidance->id }}][{{ $guidance->no_dokumen }}][{{ $guidance->nama_dokumen }}">({{ date('M Y',strtotime($guidance->month)) }}) {{ $guidance->no_dokumen }} - {{ $guidance->nama_dokumen }}</option>
                @endforeach
              </select>
              <br>
              <br>
              <a class="btn btn-info pull-right" target="_blank" style="margin-left: 5px" href="{{url('index/audit_guidance/index/'.$id)}}">
                Manage Schedule
              </a>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nama Dokumen <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="nama_dokumen" id="nama_dokumen" placeholder="Masukkan Nama Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nomor Dokumen <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="no_dokumen" id="no_dokumen" placeholder="Nomor Dokumen" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian Aktual Proses <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="kesesuaian_aktual_proses"></textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tindakan Perbaikan <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="tindakan_perbaikan" placeholder="Tindakan Perbaikan" value="-">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Target <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="target" placeholder="Pilih Tanggal Target">
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kelengkapan Point Safety <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kelengkapan_point_safety" placeholder="Kelengkapan Point Safety">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Dokumen QC Koteihyo <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="select_document_qc_koteihyo" id="select_document_qc_koteihyo" style="width: 100%;" data-placeholder="Pilih Dokumen QC Koteihyo" onchange="changeGuidance(this.value)">
                <option value=""></option>
                @foreach($documents as $documents)
                  <option value="{{ $documents->document_number }}">{{ $documents->document_number }} - {{ $documents->title }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian QC Koteihyo <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="condition_qc_koteihyo" value="Sesuai" onclick="changeQcKoteihyo(this.value)">Sesuai</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="condition_qc_koteihyo" value="Tidak Sesuai" onclick="changeQcKoteihyo(this.value)">Tidak Sesuai</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right" id="divDetailQc">
            <label class="col-sm-4">Detail Ketidaksesuaian QC Kouteihyo <span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="kesesuaian_qc_kouteihyo" style="height: 200px;" name="kesesuaian_qc_kouteihyo"></textarea>
            </div>
          </div>
          <!-- <div class="form-group row" align="right">
            <label class="col-sm-4">Hasil Keseluruhan <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="condition" value="Sesuai">Sesuai</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="condition" value="Tidak Sesuai">Tidak Sesuai</label>
              </div>
            </div>
          </div> -->
          <div class="form-group row" align="right">
            <label class="col-sm-4">Penanganan <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="handling" value="Tidak Ada Penanganan">Tidak Ada Penanganan</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Training Ulang IK">Training Ulang IK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Revisi IK">Revisi IK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Revisi QC Kouteihyo">Revisi QC Kouteihyo</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Pembuatan Jig / Repair Jig">Pembuatan Jig / Repair Jig</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="IK Tidak Digunakan">IK Tidak Digunakan</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Operator <span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <span style="color: red">Gunakan RFID Reader untuk Scan ID Card / Ketik NIK</span>
              <a href="javascript:void(0)" class="btn btn-primary" onclick="openModalOperator()">
                Masukkan Operator
              </a>
              <input type="text" name="operator" style="width: 100%;" class="form-control" id="operator" placeholder="Nama Operator" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="" value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="foreman" placeholder="" value="{{ $foreman }}" readonly>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/audit_report_activity/index/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <input class="btn btn-primary col-sm-12" type="submit" name="" value="Submit" onclick="submitForm()">
              <!-- <button type="submit" class="btn btn-primary col-sm-14">Submit</button> -->
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="box box-success">
    <div class="box-body">
      <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
        <center style="background-color: lightgreen">
          <span style="font-size: 20px;font-weight: bold;padding: 5px;">DOKUMEN IK</span>
        </center>
        <div id="document_ik" style="border: 1px solid black">
          
        </div>
      </div>
      <div class="col-xs-6" style="padding-left: 5px;padding-right: 0px">
        <center style="background-color: burlywood">
          <span style="font-size: 20px;font-weight: bold;padding: 5px;">DOKUMEN QC KOTEIHYO</span>
        </center>
        <div id="document_qc_koteihyo" style="border: 1px solid black">
          
        </div>
      </div>
    </div>
  </div>


<div class="modal fade" id="operator-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Scan Operator / Ketik NIK</b></h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <div class="col-xs-12">
            <div class="row">
              <center><span style="color: red;text-align: center;font-weight: bold;">Silahkan Scan lebih dari 1 Operator / Ketik NIK</span></center>
              <input type="text" id="scan_operator" placeholder="Scan ID Card Di sini / Ketik NIK" style="width: 100%;font-size: 20px;text-align:center;">
              <input type="text" id="operator_on_modal" placeholder="" style="width: 100%;font-size: 20px;text-align:center;">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" style="width: 100%;font-size: 20px;font-weight: bold" onclick="selesaiOperator()">
          SELESAI
        </button>
      </div>
    </div>
  </div>
</div>
  @endsection

  @section('scripts')
  <script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
  <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    $('#date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    jQuery(document).ready(function() {
      $('#divDetailQc').hide();
      $("#document_ik").empty();
      $("#document_qc_koteihyo").empty();
      $('body').toggleClass("sidebar-collapse");
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('kesesuaian_qc_kouteihyo' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function changeQcKoteihyo(value) {
      if (value == 'Sesuai') {
        $('#divDetailQc').hide();
        CKEDITOR.instances.kesesuaian_qc_kouteihyo.setData('<p>'+value+'</p>');
      }else{
        $('#divDetailQc').show();
        CKEDITOR.instances.kesesuaian_qc_kouteihyo.setData('');
      }
    }
    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          $('#blah').show();
          $('#blah')
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      const form = document.getElementById('form_audit_ik');

      form.addEventListener('submit', (e) => {
        $('#loading').show();
      });
    }

    function openModalOperator() {
      $('#operator-modal').modal('show');
      $('#scan_operator').val('');
      $('#operator_on_modal').val($('#operator').val());
      $('#scan_operator').focus();
    }

    $('#scan_operator').keydown(function(event) {
      if (event.keyCode == 13 || event.keyCode == 9) {
        if($("#scan_operator").val().length >= 8){
          var data = {
            employee_id : $("#scan_operator").val(),
          }
          
          $.get('{{ url("scan/audit_report_activity/participant") }}', data, function(result, status, xhr){
            if(result.status){
              if ($('#operator_on_modal').val() == '') {
                $('#operator_on_modal').val(result.employee.name);
              }else{
                var emp = $('#operator_on_modal').val().split(',');
                emp.push(result.employee.name);
                $('#operator_on_modal').val('');
                $('#operator_on_modal').val(emp.join(','));
              }
              $('#scan_operator').val('');
            }
            else{
              $('#scan_operator').val('');
            }
          });
        }
        else{
          $("#scan_operator").val("");
        }     
      }
    });

    function selesaiOperator() {
      $('#operator').val($('#operator_on_modal').val());
      $('#scan_operator').val('');
      $('#operator_on_modal').val('');
      $('#operator-modal').modal('hide');
    }

    function changeGuidance(value) {
      var values = value.split('][');
      $('#nama_dokumen').val($('#audit_guidance_id').val().split('][')[2]);
      $('#no_dokumen').val($('#audit_guidance_id').val().split('][')[1]);

      $("#document_ik").empty();
      $("#document_qc_koteihyo").empty();

      var data = {
        document_number_ik:$('#audit_guidance_id').val().split('][')[1],
        document_number_qc_koteihyo:$('#select_document_qc_koteihyo').val(),
      }

      $.get('{{ url("fetch/audit_report_activity/qc_koteihyo") }}', data, function(result, status, xhr){
        if(result.status){
          if (result.ik != null) {
            var file_path = '{{url("files/standardization/documents/")}}';
            var check_file_ik = doesFileExist(file_path +"/"+ result.ik.file_name_pdf);
 
            if (check_file_ik == true) {
              if(result.ik.file_name_pdf.includes('.pdf')){
                $('#document_ik').append("<embed src='"+ file_path +"/"+ result.ik.file_name_pdf +"' type='application/pdf' width='100%' height='600px'>");
              }
            } else {
                $('#document_ik').append("<center><span style='font-weight:bold;font-size:30px;color:black'>File Not Found</span></center>");
            }
          }else{
            $('#document_ik').append("<center><span style='font-weight:bold;font-size:30px;color:black'>File Not Found</span></center>");
          }

          if (result.qc_koteihyo != null) {
            var file_path = '{{url("files/standardization/documents/")}}';
            var check_file_qc_koteihyo = doesFileExist(file_path +"/"+ result.qc_koteihyo.file_name_pdf);
 
            if (check_file_qc_koteihyo == true) {
              if(result.qc_koteihyo.file_name_pdf.includes('.pdf')){
                $('#document_qc_koteihyo').append("<embed src='"+ file_path +"/"+ result.qc_koteihyo.file_name_pdf +"' type='application/pdf' width='100%' height='600px'>");
              }
            } else {
                $('#document_qc_koteihyo').append("<center><span style='font-weight:bold;font-size:30px;color:black'>File Not Found</span></center>");
            }
          }else{
            $('#document_qc_koteihyo').append("<center><span style='font-weight:bold;font-size:30px;color:black'>File Not Found</span></center>");
          }
        }
        else{
          $("#document_ik").empty();
          $("#document_qc_koteihyo").empty();
          // openErrorGritter('Error!',result.message);
        }
      });
    }

    function doesFileExist(urlToFile) {
      var xhr = new XMLHttpRequest();
      xhr.open('HEAD', urlToFile, false);
      xhr.send();
       
      if (xhr.responseURL.includes('404')) {
          return false;
      } else {
          return true;
      }
  }

    function submitForm() {
      $('#loading').show();
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
  </script>
  @stop

