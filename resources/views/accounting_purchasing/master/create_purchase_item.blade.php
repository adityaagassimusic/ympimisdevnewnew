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

  input.currency {
    text-align: left;
    padding-right: 15px;
  }

  .radio {
    display: inline-block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 16px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  /* Hide the browser's default radio button */
  .radio input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }

  /* Create a custom radio button */
  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #ccc;
    border-radius: 50%;
  }

  /* On mouse-over, add a grey background color */
  .radio:hover input ~ .checkmark {
    background-color: #ccc;
  }

  /* When the radio button is checked, add a blue background */
  .radio input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* Create the indicator (the dot/circle - hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the indicator (dot/circle) when checked */
  .radio input:checked ~ .checkmark:after {
    display: block;
  }

  /* Style the indicator (dot/circle) */
  .radio .checkmark:after {
    top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
  }
  
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Create Purchasing Item
    <small>{{ $title_jp }}</small>
  </h1>
  <ol class="breadcrumb">
    <a href="{{ url("index/purchase_item/create_category")}}" class="btn btn-md bg-blue" target="_blank" style="color:white">
      <i class="fa fa-plus"></i> Create New Item Category
    </a>
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
      <h2 class="box-title"><b>Purchase Item</b></h2>
    </div>  
    <form role="form" id="createdfrom">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <label class="col-sm-2 col-md-offset-1">Kategori Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id="item_category" name="item_category" data-placeholder='Category' style="width: 100%" onchange="pilihKategori(this)">
              <option value="">&nbsp;</option>
              @foreach($item_category as $cat)
              <option value="{{$cat->category_id}}">{{$cat->category_id}} - {{$cat->category_name}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Kode Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" class="form-control" id="item_code" name="item_code" placeholder="Kode Item">
          </div>
        </div>
        
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Deskripsi Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_desc" name="item_desc" class="form-control" placeholder="Deskripsi">
          </div>
        </div>
        
        <!-- <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Spesifikasi Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_spec" class="form-control" placeholder="Spesifikasi">
          </div>
        </div> -->

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Currency<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id="item_currency" name="item_currency" data-placeholder='Currency' style="width: 100%" onchange="currency()">
              <option value="">&nbsp;</option>
              <option value="USD">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY">JPY</option>
            </select>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Harga Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <div class="input-group"> 
              <span class="input-group-addon" id="ket_harga">?</span>
              <input type="text" id="item_price" name="item_price" class="form-control" placeholder="Harga" >
            </div>
            
            <!-- <input type="number" id="item_price" name="item_price" class="form-control" min="0.01" max="2500.00" value="25.00" /> -->
          </div>
        </div>
        
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Leadtime<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <div class="input-group">
              <input type="text" id="item_leadtime" name="item_leadtime" class="form-control" placeholder="Leadtime">
              <span class="input-group-addon">Day (s)</span>
            </div>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">UOM<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id='item_uom' name="item_uom" data-placeholder="Select UOM" style="width: 100%;">
              <option></option>
              @foreach($uom as $um)
              <option value="{{ $um }}">{{ $um }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Lot<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_lot" name="item_lot" class="form-control" placeholder="Lot">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">MOQ<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_moq" name="item_moq" class="form-control" placeholder="MOQ">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Foto</label>
          <div class="col-sm-6" align="left">
            <input type="file" id="item_file" name="item_file">
          </div>
        </div>

        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/purchase_item') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-success pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
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
clearAll();

    });

    $(function () {
      $('.select2').select2()
    });

    function clearAll(){
      $("#item_category").val("").trigger('change');
      $("#item_code").val("").trigger('change');
      $("#item_desc").val("");
      $("#item_currency").val("").trigger('change');
      $("#item_price").val("");
      $("#item_leadtime").val("");
      $("#item_uom").val("").trigger('change');
      $("#item_lot").val("");
      $("#item_moq").val("");
      $("#item_file").val("");
    }

    function currency(){

      var mata_uang = $('#item_currency').val();

      if (mata_uang == "USD") {
        $('#ket_harga').text("$");
        var harga = document.getElementById("item_price");
            // harga.addEventListener("keyup", function(e) {
            //   harga.value = formatRupiah(this.value, "");
            // });
          }

          else if (mata_uang == "IDR") {
            $('#ket_harga').text("Rp. ");

            var harga = document.getElementById("item_price");
          // harga.addEventListener("keyup", function(e) {
          //   harga.value = formatRupiah(this.value, "");
          // });
        }

        else if (mata_uang == "JPY") {
          $('#ket_harga').text("¥");

          var harga = document.getElementById("item_price");
          // harga.addEventListener("keyup", function(e) {
          //   harga.value = formatRupiah(this.value, "");
          // });
        }
      }

      /* Fungsi formatRupiah */
      function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
    }

    function pilihKategori(elem)
    {
      var category = $('#item_category').val();
      var kode_item = document.getElementById("item_code");

      $.ajax({
        url: "{{ url('index/purchase_item/get_kode_item') }}?kategori="+category,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);

          var no = obj.no_urut;
          kode_item.value = category + no;
        }
      });
    }

    $('.datepicker').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      orientation: 'bottom auto',
    });


    // $("#form_submit").click( function() {
    //   $("#loading").show();

    //   if ($("#item_code").val() == "") {
    //     $("#loading").hide();
    //     alert("Kode Item Tidak Boleh Kosong");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_category").val() == "") {
    //     $("#loading").hide();
    //     alert("Kategori Item Tidak Boleh Kosong");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_desc").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom Deskripsi Item Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_currency").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom Currency Item Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_price").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom Harga Item Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_leadtime").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom Leadtime Item Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_uom").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom UOM Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_lot").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom Lot Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   if ($("#item_moq").val() == "") {
    //     $("#loading").hide();
    //     alert("Kolom MOQ Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   console.log($("#item_file").val());

    //   if ($("#item_file").val() == "") {
    //     $("#loading").hide();
    //     alert("Foto Harap diisi");
    //     $("html").scrollTop(0);
    //     return false;
    //   }

    //   var price = $("#item_price").val();

    //   // var data = {
    //   //   item_code: $("#item_code").val(),
    //   //   item_category: $("#item_category").val(),
    //   //   item_desc: $("#item_desc").val(),
    //   //   // item_spec: $("#item_spec").val(),
    //   //   item_currency: $("#item_currency").val(),
    //   //   item_price: price,
    //   //   item_leadtime: $("#item_leadtime").val(),
    //   //   item_uom: $("#item_uom").val(),
    //   //   item_lot: $("#item_lot").val(),
    //   //   item_moq: $("#item_moq").val(),
    //   //   item_file: $("#item_file").val()

    //   // };
    //   // $.post('{{ url("index/purchase_item/create_post") }}', data, function(result, status, xhr){
    //   //   if(result.status == true){    
    //   //     $("#loading").hide();
    //   //     openSuccessGritter("Success","Berhasil Dibuat");
    //   //     // setTimeout(function(){ window.location = "{{ url('index/purchase_item') }}" }, 2000); 

    //   //     setTimeout(function(){  window.location = "{{url('index/purchase_item/update')}}/"+result.id; }, 1000); 
    //   //   }
    //   //   else {
    //   //     $("#loading").hide();
    //   //     openErrorGritter('Error!', result.datas);
    //   //   }

    //   // });

    // });


    $("form#createdfrom").submit(function(e){

      if ($("#item_code").val() == "") {
        $("#loading").hide();
        alert("Kode Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_category").val() == "") {
        $("#loading").hide();
        alert("Kategori Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_desc").val() == "") {
        $("#loading").hide();
        alert("Kolom Deskripsi Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_currency").val() == "") {
        $("#loading").hide();
        alert("Kolom Currency Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_price").val() == "") {
        $("#loading").hide();
        alert("Kolom Harga Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_leadtime").val() == "") {
        $("#loading").hide();
        alert("Kolom Leadtime Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_uom").val() == "") {
        $("#loading").hide();
        alert("Kolom UOM Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_lot").val() == "") {
        $("#loading").hide();
        alert("Kolom Lot Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_moq").val() == "") {
        $("#loading").hide();
        alert("Kolom MOQ Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      // if ($("#item_file").val() == "") {
      //   $("#loading").hide();
      //   alert("Foto Harap diisi");
      //   $("html").scrollTop(0);
      //   return false;
      // }

      if(confirm("Apakah anda yakin akan membuat Purchasing Item ini?")){
        $("#loading").show();


      // $("#create_btn").attr("disabled", true);
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: '{{ url("index/purchase_item/create_post") }}',
        type: 'POST',
        enctype: 'multipart/form-data',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function (result, status, xhr) {
          if(result.status) {
            openSuccessGritter("Success", 'Berhasil Dibuat');
            $("#loading").hide();
            location.reload();
            clearAll();
            // setTimeout(function(){  window.location = "{{url('index/purchase_item/update')}}/"+result.id; }, 1000); 
          } else {
            $("#create_btn").prop("disabled", false);
            $("#loading").hide();
            openErrorGritter("Error", "Failed To Create Data (Reason : Photo/Special Character)");
          }
        },
        function (xhr, ajaxOptions, thrownError) {
          $("#create_btn").prop("disabled", false);
          openErrorGritter(xhr.status, thrownError);
        }
      })
    }else{
      return false;
    }
  });



    function modalDelete(id) {
      var data = {
        id: id
      };

      alert("s");

    
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

