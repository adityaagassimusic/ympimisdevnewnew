@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Edit Audit IK
  </h1>
  <ol class="breadcrumb">
 </ol>
</section>
@endsection
@section('content')
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


  <!-- SELECT2 EXAMPLE -->
  <div class="box box-solid">
    <form role="form" method="post" action="{{url('index/audit_report_activity/update/'.$id.'/'.$audit_report_activity->id)}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Department<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="department" placeholder="Enter Department" required value="{{ $departments }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Section<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="section" style="width: 100%;" data-placeholder="Choose a Section..." required>
                <option value=""></option>
                @foreach($section as $section)
                  @if($audit_report_activity->section == $section->section_name)
                    <option value="{{ $section->section_name }}" selected>{{ $section->section_name }}</option>
                  @else
                    <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Group<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group..." required>
                <option value=""></option>
                @foreach($subsection as $subsection)
                  @if($audit_report_activity->subsection == $subsection->sub_section_name)
                    <option value="{{ $subsection->sub_section_name }}" selected>{{ $subsection->sub_section_name }}</option>
                  @else
                    <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Audit Schedule<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="audit_guidance_id" style="width: 100%;" data-placeholder="Pilih Schedule ..." required readonly>
                <option value=""></option>
                @foreach($guidance as $guidance)
                  @if($audit_report_activity->audit_guidance_id == $guidance->id)
                    <option value="{{ $guidance->id }}" selected>({{ date('M Y',strtotime($guidance->month)) }}) {{ $guidance->no_dokumen }} - {{ $guidance->nama_dokumen }}</option>
                  @else
                    <option value="{{ $guidance->id }}">{{ $guidance->no_dokumen }} - {{ $guidance->nama_dokumen }}</option>
                  @endif
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
            <label class="col-sm-4">Nama Dokumen<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="nama_dokumen" placeholder="Enter Nama Dokumen" required value="{{ $audit_report_activity->nama_dokumen }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Nomor Dokumen<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="no_dokumen" placeholder="Nomor Dokumen" required value="{{ $audit_report_activity->no_dokumen }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian Aktual Proses<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="kesesuaian_aktual_proses">{{ $audit_report_activity->kesesuaian_aktual_proses }}</textarea>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tindakan Perbaikan<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="tindakan_perbaikan" placeholder="Tindakan Perbaikan" value="{{ $audit_report_activity->tindakan_perbaikan }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Target<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="target" value="{{ $audit_report_activity->target }}">
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kelengkapan Point Safety<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kelengkapan_point_safety" placeholder="Kelengkapan Point Safety" value="{{ $audit_report_activity->kelengkapan_point_safety }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kesesuaian QC Kouteihyo<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" name="kesesuaian_qc_kouteihyo" placeholder="Kesesuaian QC Kouteihyo" value="{{ $audit_report_activity->kesesuaian_qc_kouteihyo }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Hasil Keseluruhan</label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="condition" value="Sesuai" @if($audit_report_activity->condition == "Sesuai") checked @endif >Sesuai</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="condition" value="Tidak Sesuai" @if($audit_report_activity->condition == "Tidak Sesuai") checked @endif >Tidak Sesuai</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Penanganan</label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="handling" value="Tidak Ada Penanganan" @if($audit_report_activity->handling == "Tidak Ada Penanganan") checked @endif>Tidak Ada Penanganan</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Training Ulang IK" @if($audit_report_activity->handling == "Training Ulang IK") checked @endif>Training Ulang IK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Revisi IK" @if($audit_report_activity->handling == "Revisi IK") checked @endif>Revisi IK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Revisi QC Kouteihyo" @if($audit_report_activity->handling == "Revisi QC Kouteihyo") checked @endif>Revisi QC Kouteihyo</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="Pembuatan Jig / Repair Jig" @if($audit_report_activity->handling == "Pembuatan Jig / Repair Jig") checked @endif>Pembuatan Jig / Repair Jig</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="handling" value="IK Tidak Digunakan" @if($audit_report_activity->handling == "IK Tidak Digunakan") checked @endif>IK Tidak Digunakan</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Operator<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <!-- <select class="form-control select2" name="operator" style="width: 100%;" data-placeholder="Choose an Operator..." required>
                <option value=""></option>
                @foreach($operator as $operator)
                @if($audit_report_activity->operator == $operator->name)
                  <option value="{{ $operator->name }}" selected>{{ $operator->employee_id }} - {{ $operator->name }}</option>
                @else
                  <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
                @endif
                @endforeach
              </select> -->
              <a href="javascript:void(0)" class="btn btn-primary" onclick="openModalOperator()">
                Edit Operator
              </a>
              <input type="text" name="operator" style="width: 100%;" class="form-control" id="operator" placeholder="Nama Operator" value="{{$audit_report_activity->operator}}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="" value="{{ $audit_report_activity->leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="foreman" placeholder="" value="{{ $audit_report_activity->foreman }}" readonly>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/audit_report_activity/index/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Update</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<div class="modal fade" id="operator-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Scan Operator</b></h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <div class="col-xs-12">
            <div class="row">
              <center><span style="color: red;text-align: center;font-weight: bold;">Silahkan Scan lebih dari 1 Operator</span></center>
              <input type="text" id="scan_operator" placeholder="Scan ID Card Here ..." style="width: 100%;font-size: 20px;text-align:center;">
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
      $('body').toggleClass("sidebar-collapse");
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editor2' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
  </script>
  <script language="JavaScript">
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
  </script>
  @stop

