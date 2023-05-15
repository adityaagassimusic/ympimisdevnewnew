@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Buat Audit
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
    <form role="form" method="post" action="{{url('index/production_audit/store/'.$id.'/'.$product.'/'.$proses)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="product" id="product" class="form-control" value="{{ $product }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="proses" id="proses" class="form-control" value="{{ $proses }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right" id="form_point_check">
            <label class="col-sm-4">Point Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              @if($point_check_audit != null)
                <textarea name="point_check_details" class="form-control" style="height: 250px;" id="editor1" readonly>{{ $point_check_audit->point_check }}</textarea>
                <input type="hidden" name="point_check" value="{{ $point_check_audit->id }}">
              @else
                <select class="form-control select2" name="point_check" style="width: 100%;" data-placeholder="Pilih Point Check..." required id="point_check">
                  <option value=""></option>
                </select>
              @endif
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foto Kondis Aktual<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="file" name="file" id="inputFile" class="form-control" value="" onchange="readURL(this);">
              <br>
              <img width="200px" id="blah" src="" style="display: none" alt="your image" />
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              {{-- <select class="form-control select2" name="kondisi" style="width: 100%;" data-placeholder="Choose a Kondisi..." required>
                <option value=""></option>
                <option value="Good">Good</option>
                <option value="Not Good">Not Good</option>
              </select> --}}
              <div class="radio">
                <label><input type="radio" name="kondisi" value="Good">OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi" value="Not Good">NG</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">PIC<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="pic" style="width: 100%;" data-placeholder="Pilih PIC" required>
                <option value=""></option>
                @foreach($pic as $pic)
                  <option value="{{ $pic->employee_id }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Auditor<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              {{-- <select class="form-control select2" name="auditor" style="width: 100%;" data-placeholder="Choose a Auditor..." required>
                <option value=""></option>
                @foreach($leaderForeman2 as $leaderForeman2)
                  <option value="{{ $leaderForeman2->employee_id }}">{{ $leaderForeman2->employee_id }} - {{ $leaderForeman2->name }}</option>
                @endforeach
              </select> --}}
              <input type="text" name="auditor" id="auditor_contoh" class="form-control" value="{{ $empid_leader }} - {{ $leader }}" readonly>
              <input type="hidden" name="auditor" id="auditor" class="form-control" value="{{ $empid_leader }}">
            </div>
          </div>
        </div>
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/production_audit/index/'.$id.'/'.$product.'/'.$proses) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
            </div>
          </div>
      </div>
    </form>
  </div>
  @endsection

  @section('scripts')
  
  <script type="text/javascript">
        // $("#form_point_check").hide();
        // $("#proses").change(function(){
          // $("#form_point_check").show();
          // console.log($(this).val());
          // console.log($("#product").val());
            $.ajax({
                url: "{{ route('admin.cities.get_by_country') }}?proses=" + $("#proses").val()+"&product="+ $("#product").val(),
                method: 'GET',
                success: function(data) {
                    $('#point_check').html(data.html);
                }
            });
        // });
    </script>
  <script>
    
    // var product = document.getElementById("product");
    // var proses = document.getElementById("proses");

    // var productselected= product.options[product.selectedIndex];
    // var prosesselected= proses.options[proses.selectedIndex];

    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
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

