@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<section class="content-header">
  <h1>
    Create {{ $activity_name }}
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
    <form role="form" method="post" action="{{url('index/interview/store/'.$id)}}" enctype="multipart/form-data">
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
              <select class="form-control select2" name="section" style="width: 100%;" data-placeholder="Pilih Section..." required>
                <option value=""></option>
                @foreach($section as $section)
                <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
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
                <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Date<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="date" placeholder="Enter Date" required value="{{ date('Y-m-d') }}" readonly>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <!-- <div class="form-group row" align="right">
            <label class="col-sm-4">Periode<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left"> -->
              <!-- <select class="form-control select2" name="periode" style="width: 100%;" data-placeholder="Pilih Periode..." required>
                <option value=""></option>
                @foreach($fy as $fy)
                <option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
                @endforeach
              </select> -->
              <input type="hidden" class="form-control" name="periode" placeholder="Enter Date" required value="{{ $fy->fiscal_year }}" readonly>
              <input type="hidden" class="form-control" name="status" id="status" placeholder="Enter Date" required value="leader" readonly>
            <!-- </div>
          </div> -->
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="leader" placeholder="Enter Tujuan" required value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="foreman" placeholder="Enter Tujuan" required value="{{ $foreman }}" readonly>
            </div>
          </div>
          <div class="col-sm-4 col-sm-offset-6">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/interview/index/'.$id) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
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
      $('#email').val('');
      $('#password').val('');
      $('body').toggleClass("sidebar-collapse");
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
  </script>
  @stop

