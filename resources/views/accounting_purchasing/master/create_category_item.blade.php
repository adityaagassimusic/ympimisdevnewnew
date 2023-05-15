@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .col-xs-1,
  .col-xs-2,
  .col-xs-3,
  .col-xs-4,
  .col-xs-5,
  .col-xs-6,
  .col-xs-7,
  .col-xs-8,
  .col-xs-9,
  .col-xs-10 {
    padding-top: 5px;
  }

  .containers {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    padding-top: 6px;
  }

  /* Hide the browser's default checkbox */
  .containers input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }


  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
    margin-top: 4px;
  }

  /* On mouse-over, add a grey background color */
  .containers:hover input ~ .checkmark {
    background-color: #ccc;
  }

  /* When the checkbox is checked, add a blue background */
  .containers input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the checkmark when checked */
  .containers input:checked ~ .checkmark:after {
    display: block;
  }

  /* Style the checkmark/indicator */
  .containers .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
  
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Create Category Item
    <small>{{ $title_jp }}</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header" style="margin-top: 10px;text-align: center">
      <h2 class="box-title"><b>Kategori Item</b></h2>
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-2">ID Kategori<span class="text-red">*</span></label>
          <div class="col-sm-5" align="left">
            <input type="text" id="category_id" name="category_id" class="form-control" placeholder="ID Kategori">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-2">Nama Kategori<span class="text-red">*</span></label>
          <div class="col-sm-5" align="left">
            <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Nama Kategori">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-2">Kategori</label>
          <div class="col-sm-5" align="left">
            <label class="containers" onclick="barang_modal()">Barang Modal
              <input type="checkbox" id="category" name="category" value="">
              <span class="checkmark"></span>
            </label>
          </div>
        </div>


        <div class="row" style="margin-top: 10px;display: none;" id="group_div" >
          <label class="col-sm-2 col-md-offset-2">Grup</label>
          <div class="col-sm-5" align="left">
            <input type="text" id="group" name="group" class="form-control" placeholder="Nama Grup">
          </div>
        </div>

        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/purchase_item') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-success pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });
    });

</script>
  <script>
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    jQuery(document).ready(function() {
      $("#navbar-collapse").text('');
        $('.select2').select2({
          language : {
            noResults : function(params) {
              // return "There is no cpar with status 'close'";
            }
          }
        });
    });

    $(function () {
      $('.select2').select2()
    });

    $('.datepicker').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      orientation: 'bottom auto',
    });


    $("#form_submit").click( function() {
      $("#loading").show();

      if ($("#category_id").val() == "") {
        $("#loading").hide();
        alert("ID Kategori Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#category_name").val() == "") {
        $("#loading").hide();
        alert("Nama Kategori Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        category_id: $("#category_id").val(),
        category_name: $("#category_name").val(),
        category: $("#category").val(),
        group: $("#group").val(),
      };

      $.post('{{ url("index/purchase_item/create_category") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Dibuat");
          setTimeout(function(){ window.location = "{{ url('index/purchase_item') }}" }, 1000); 
        }
        else {
          $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
        
      });

    });


  function barang_modal() {
    var returns = '';
    $("input[name='category']:checked").each(function (i) {
      returns = "Barang Modal";
    });

    if(returns == "Barang Modal"){
      $("#category").val(returns); 
      $("#group_div").show();       
    }else{
      $("#category").val(""); 
      $("#group_div").hide();
    }


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
          time: '2000'
        });
      }

  </script>
@stop

