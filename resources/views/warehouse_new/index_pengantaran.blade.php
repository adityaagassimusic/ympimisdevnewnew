@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">

<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
  }
  thead>tr>th>input{
    text-align:center;
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
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    font-size: 14px;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:2px;
    font-size: 18px;
    text-align: center;
    cursor: pointer;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    font-size: 1vw;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ffd8b7;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #FFD700;
  }

  .tombol {
    font-weight: bold;
    font-size: 16px;
  }

  .td_tombol {
    padding: 5px;
  }
  .cob {
    width: 80px !important;
  }
  .cobk {
    width: 60px !important;
  }
  .cos {
    width: 100px !important;
  }

  #loading, #error { display: none; }

</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop

@section('content')
<section class="content" style="padding: 0;">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: white; top: 45%; left: 35%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid" style="margin:0">
        <div class="box-body">
          <div class="col-xs-12" style="background-color: rgb(65, 114, 166); margin-bottom: 20px;">
            <h2 style="text-align: center; margin:5px; font-weight: bold; color: white">CHECK IN PENGANTARAN REQUEST</h2>
          </div>
          <table class="table table-bordered" id="tableResume">
            <thead style="background-color: rgb(65, 114, 166); color: white;">
              <tr>
                <th width="3%">No</th>
                <th width="10%">Kode Request</th>
                <th width="17%">PIC Request</th>
                <th width="17%">PIC Pelayanan</th>
                <th width="10%">GMC</th>
                <th width="20%">Description</th>
                <th width="12%">Quantity Request</th>
                <th width="12%">Quantity Kirim</th>
                <th width="15%">Lokasi Kirim</th>
                <th width="13%">Status</th>
                <th width="10%">Check<input onClick="checkAll(this)" type="checkbox" id="checkAllBox"/> </th>
              </tr>
            </thead>
            <tbody id="tableBodyResume1">
            </tbody> 
          </table>
        </div>
        <button class="btn btn-primary" id="btn_antars" style="margin-left:1%; width: 98%; font-size: 22px; margin-bottom: 30px;" onclick="UpdatePengantaran(this)"><i class="fa fa-paper-plane"></i> Antar</button>
      </div>
    </div>
  </div>

  <div class="row" style="padding-top: 10px;">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <div class="col-xs-12" style="background-color: rgb(143, 71, 132); margin-bottom: 20px;">
            <h2 style="text-align: center; margin:5px; font-weight: bold; color: white">CHECK OUT PENGANTARAN REQUEST KE PRODUKSI</h2>
          </div>
          <table id="cek_lokasi" class="table table-hover table-striped table-bordered">
            <thead style="background-color: rgb(65, 114, 166); color: white;">
              <tr>
               <th width="10%">Kode Request</th>
               <th width="10%">Lokasi Awal</th>
               <th width="10%">Lokasi Tujuan</th>
               <th width="10%">Total Material</th>
               <th width="10%">Detail</th>
               <th width="10%">Check</th>
             </tr>
           </thead>
           <tbody id="cek_lokasi_body">
           </tbody>
         </table>
       </div>
     </div>
   </div>
 </div>

 <div class="modal fade" id="modalDetail">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #3c8dbc;">
          <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL MATERIAL REQUEST</h1>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table id="detail_material" class="table table-hover table-striped table-bordered" style="font-weight: bold;"> 
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th class="cob">Kode Request</th>
                  <th class="cob">PIC Request</th>
                  <th class="cobk">No Kanban</th>
                  <th style="width: 3%;">GMC</th>
                  <th class="cos">Description</th>
                  <th style="width: 3%;">lot</th>
                  <th style="width: 3%;">Uom</th>
                  <th class="cos">Quantity Kirim</th>
                </tr>
              </thead>
              <tbody id="detail_material_body">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<div class="modal fade" id="modalScan" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"><b>SCAN QR AREA PRODUKSI</b></h4>
      </div>
      <div class="modal-body">
        <div class="input-group col-md-8 col-md-offset-2">
          <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
            <i class="glyphicon glyphicon-barcode"></i>
          </div>
          <input type="text" style="text-align: center; font-size: 15" class="form-control" id="scan_qrcode_material2" name="scan_qrcode_material2" placeholder="Scan Material Request" required>
          <div class="input-group-addon" id="icon-serial">
            <i class="glyphicon glyphicon-ok"></i>
          </div>
          <div class="input-group-btn">
            <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" onclick="showCam()"><i class="fa fa-qrcode"></i> Scan Camera </button>
          </div>
        </div>
        <div class="modal-body table-responsive no-padding">
          <div id='scanner' class="col-xs-12">
            <div class="col-xs-12">
              <center>
                <div id="loadingMessage1">
                  🎥 Unable to access video stream
                  (please make sure you have a webcam enabled)
                </div>
                <video autoplay muted playsinline id="video2"></video>
                <div id="output" hidden>
                  <div id="outputMessage">No QR code detected.</div>
                </div>
              </center>
            </div>                  
          </div>
        </div>

        <!-- <p style="visibility: hidden;">camera</p> -->
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="scanModalLogout" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"><b>SCAN QR AREA</b></h4>
      </div>
      <div class="modal-body">
        <div class="input-group col-md-8 col-md-offset-2">
          <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
            <i class="glyphicon glyphicon-barcode"></i>
          </div>
          <input type="text" style="text-align: center; font-size: 15" class="form-control" id="scan_qrcode_material" name="scan_qrcode_material" placeholder="Scan Material Request" required>
          <div class="input-group-addon" id="icon-serial">
            <i class="glyphicon glyphicon-ok"></i>
          </div>
          <div class="input-group-btn">
            <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" onclick="showCam()"><i class="fa fa-qrcode"></i> Scan Camera </button>
          </div>
        </div>

        <div class="modal-body table-responsive no-padding">
          <div id='scanner2' class="col-xs-12">
            <div class="col-xs-12">
              <center>
                <div id="loadingMessage">
                  🎥 Unable to access video stream
                  (please make sure you have a webcam enabled)
                </div>
                <video autoplay muted playsinline id="video"></video>
                <div id="output" hidden>
                  <div id="outputMessage">No QR code detected.</div>
                </div>
              </center>
            </div>                  
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="col-md-6" hidden>
  <div class="input-group input-group-lg">
    <div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
      <i class="fa fa-qrcode"></i>
    </div>
    <input type="hidden" class="form-control" placeholder="SCAN QR AREA" id="item_scan">
    <div class="input-group-btn">
      <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modalScan"><i class="fa fa-qrcode"></i> Scan QR</button>
    </div>
  </div>
  <br>
  <input type="hidden" id="code">
  <input type="hidden" id="loc">
  <input type="hidden" id="lokasi">
  <input type="hidden" id="id_button">
  <input type="hidden" id="login_time">
  <input type="hidden" id="lok_prod">
  <input type="hidden" id="area_request">
  <input type="hidden" id="detail">
  <input type="hidden" id="lokasi_awal">
  <input type="hidden" id="tag_checkbox">
  <input type="hidden" id="check_code">
  <input type="hidden" id="check_code2">
</div>


<div class="modal fade" id="process" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <div class="col-xs-12" style="background-color: #757ce8;color: white;">
      <h1 style="text-align: center; margin:5px; font-weight: bold;">Processes 工程</h1>
    </div>
    <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
      <center><h4 id="title_proses" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
      <table width="100%">
        <tbody align="center" id='body_button'>
        </tbody>            
      </table>
    </div>    
  </div>
</div>
</div>
</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/jsQR.js") }}"></script>


<script>


  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  var button = 1;
  var area;
  var loc;
  var lok_prod;
  var area_request;
  var lokasi;
  var detail;
  var video;
  var video2;

  var detail_all = [];
  var detail_all1 = [];
  var cek_all = [];
  var cek_all2 = [];
    var code_lok = [];





  var lokasi1 = <?php echo json_encode($lokasi1); ?>;

  jQuery(document).ready(function() {
   getData();
   fetchCekLokasi()
   fetchAll();
   // getDataCheckout();

   $('#btn_antars').hide();

   $("#item_scan").val("");
   $("#code").val("");
   $("#loc").val("");
   $("#lokasi").val("");
   $("#lok_prod").val("");
   $("#area_request").val("");
   $("#login_time").val("");
   $("#lokasi_awal").val("");
   $("#tag_checkbox").val("");
 });

  function StartScan() {
    $('#modalScan').modal('show');

  }

  function stopScan() {
    $('#modalScan').modal('hide');

  }

  function videoOff() {
    video.pause();
    video.src = "";
    video.srcObject.getTracks()[0].stop();
  }


  $('#modalScan').on('hidden.bs.modal', function () {
    if ($("#check_code2").val() == "1") {
      videoOff();
      $("#scanner").hide();
      $("#check_code2").val("");
    }else{
      $("#scan_qrcode_material2").val("");
      $("#check_code2").val("");
      videoOff();
    }
  });

  $('#scanModalLogout').on('hidden.bs.modal', function () {
    videoOff();
    $("#scanner2").hide();
    $("#check_code").val("");
    
  });

  function showCam() {
    $('#scanModalLogout').modal('show');
    $('#scanner2').show();
    showCheckLogout();
    var cod = 1;
    $("#check_code").val(cod);

  }


  $('#scan_qrcode_material2').keydown(function(event) {
    var video = document.createElement("video");
    vdo = video;
    var loadingMessage = document.getElementById("loadingMessage");

    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#scan_qrcode_material2").val().length > 3){
          // scanFloNumber();
          checkCode(video, $("#scan_qrcode_material2").val());
          return false;
        }
        else{
          openErrorGritter('Error!', 'QR Code Tidak Cocok');
          $("#scan_qrcode_material2").val("");
          audio_error.play();
        }
      }
    });

  $('#scan_qrcode_material').keydown(function(event) {
    var video = document.createElement("video");
    vdo = video;
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#scan_qrcode_material").val().length > 3){
          // scanFloNumber();
          checkCodeLogout(video, $("#scan_qrcode_material").val());
          return false;
        }
        else{
          openErrorGritter('Error!', 'QR Code Tidak Cocok');
          $("#scan_qrcode_material").val("");
          audio_error.play();
        }
      }
    });
  
  function showCheck() {
    $('#scanner').show();

    var vdo = document.getElementById("video2");
    video = vdo;
    var tickDuration = 200;
    video.style.boxSizing = "border-box";
    video.style.position = "absolute";
    video.style.left = "0px";
    video.style.top = "0px";
    video.style.width = "400px";
    video.style.zIndex = 1000;

    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.play();
      setTimeout(function() {tick();},tickDuration);
    });

    function tick(){
      loadingMessage.innerText = "⌛ Loading video..."
      try{

      loadingMessage.hidden = true;
      video.style.position = "static";

      var canvasElement = document.createElement("canvas");            
      var canvas = canvasElement.getContext("2d");
      canvasElement.height = video.videoHeight;
      canvasElement.width = video.videoWidth;
      canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
      var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
      var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
      if (code) {
        outputMessage.hidden = true;
        // videoOff();
        checkCode(video, code.data);


      }else{
        outputMessage.hidden = false;
      }

      } catch (t) {
      }
      setTimeout(function() {tick();},tickDuration);
    }
  }


  function showCheckLogout() {
    $('#scanner2').show();

    var vdo = document.getElementById("video");
    video = vdo;
    var tickDuration = 200;
    video.style.boxSizing = "border-box";
    video.style.position = "absolute";
    video.style.left = "0px";
    video.style.top = "0px";
    video.style.width = "400px";
    video.style.zIndex = 1000;

    var loadingMessage1 = document.getElementById("loadingMessage1");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.play();
      setTimeout(function() {tick();},tickDuration);
    });

    function tick(){
      loadingMessage1.innerText = "⌛ Loading video...";
      try{
      loadingMessage1.hidden = true;
      video.style.position = "static";

      var canvasElement = document.createElement("canvas");            
      var canvas = canvasElement.getContext("2d");
      canvasElement.height = video.videoHeight;
      canvasElement.width = video.videoWidth;
      canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
      var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
      var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
      if (code) {
        outputMessage.hidden = true;
        // videoOff();
        checkCodeLogout(video, code.data);

      }else{
        outputMessage.hidden = false;
      }
      } catch (t) {
      }
      setTimeout(function() {tick();},tickDuration);
    }
  }

  function checkCodeLogout(video, data) {

    var data_cek = 0;
    var location_first = "";
    var tag = [];


    $("input[type=checkbox]:checked").each(function() {
      if (this.id.indexOf("All") >= 0) {

      } else {
        var id_print = this.id.split("_");
        tag.push(id_print[1]);
      }
    });


    $.each(lokasi1, function(index9, value9){
      if (data == "AWRHR02") {
        location_first = "Warehouse Internal";
      }
    });

    if(jQuery.inArray(data, code_lok) !== -1)
        {
        openErrorGritter('Error','Data sudah ada');
        }else{
        openSuccessGritter('Succes','Data belum ada');
        code_lok.push(data);
        data_cek = 1;
    }


    if (data_cek == 1) {
      $("#lokasi_awal").val(location_first);
      $('#scanModalLogout').modal('hide');
      openSuccessGritter('Success', 'Area Cocok');

      var data = {
        id : tag,
        lokasi_awal : location_first
      }

      $.post('{{ url("update/pengantaran") }}',data, function(result, status, xhr){
        if(result.status){    
          openSuccessGritter('Success', result.message);
          location.reload();
        }
        else{
          openErrorGritter('Error!', result.message);
        }

      });
    } else {
      openErrorGritter('Error', 'Area Tidak Cocok');
    }

  }

  function checkCode(video, code) {
    stat = false;
    var loc = "";
    var loc1 = $('#loc').val();

    if (code == $('#lok_prod').val()) {
      $('#scanner').hide();
      videoOff();
      openSuccessGritter('Success', 'QR Code Successfully');
      $("#modalScan").modal('hide');
      var id = $('#id_button').val();
      var ids = id.split('_');
      $('#tdscan_'+ids[1]).html('');
      $('#tdscan_'+ids[1]).html('SESUAI LOKASI');
      $('#butview_'+ids[1]).show();

      var data = {
        loc1 : loc1,
        areas : $('#area_request').val()
      }


      $.post('{{ url("post/pengantaran/lokasi") }}', data,  function(result, status, xhr){
        if(result.status == true){
          openSuccessGritter('Success','Tambah Data Berhasil');
          $('#modalStart').modal('hide'); 
          location.reload();

        }else{
          $('#loading').hide();
          openErrorGritter('Error!','Tambah Data Gagal');
        }
      });     

    }else{
      openErrorGritter('Error', 'QR Code Not Registered');
      audio_error.play();
    }

  }
  var total;


  // function countPicked(element){

  //   var id = $(element).attr("id");
  //   var checkId = id.slice(4);
  //   var checkVal = $('#'+checkId).is(":checked");

  //   if(checkVal) {
  //     total--;
  //     $('#'+ String(checkId)).prop('checked', false);
  //     // $('#tr+'+ String(checkId)).css('background-color', '#000000');

  //   }else{
  //     total++;
  //     $('#'+ String(checkId)).prop('checked', true);
  //     // $('#tr+'+ String(checkId)).toggleClass('active');
  //   }

  //   $("#picked").html(total);
  // } 

  function countPicked(element){

    var id = $(element).attr("id");

    console.log(id);

    var checkDisabled = $('#print_'+id).prop("disabled");
    // console.log(checkDisabled);

    if(checkDisabled == undefined){

    }
    else{
      var checkVal = $('#print_'+id).is(":checked");
      // console.log(checkVal);
      if(checkVal) {
        total--;
        $('#print_'+ String(id)).prop('checked', false);

      }else{
        total++;
        $('#print_'+ String(id)).prop('checked', true);
      }
    }
    $("#picked").html(total);
  } 


  function getData(){
    $.get('<?php echo e(url("fetch/pengantaran/pelayanan")); ?>', function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        // var tableData = '';
        $('#tableBodyResume1').html("");
        $('#tableBodyResume1').empty();
        
        var body = '';
        var css = 'style="background-color: #000000;"';
        var pic_antar = "";
        count=1

        for (var i = 0; i < result.pengantaran.length; i++) {
          console.log(result.pengantaran[i].pic_pelayanan);

          if (result.pengantaran[i].pic_pelayanan == "pi0909001" || result.pengantaran[i].pic_pelayanan == "pi2009005") {
            pic_antar = 'style="background-color: #6fdaed"';
          }else if (result.pengantaran[i].pic_pelayanan == "pi0508001" || result.pengantaran[i].pic_pelayanan == "pi0004006") {
            pic_antar = 'style="background-color: #7ef547"';
          }else if (result.pengantaran[i].pic_pelayanan == "pi2001039" || result.pengantaran[i].pic_pelayanan == "pi1803032") {
            pic_antar = 'style="background-color: #bbff9c"';
          }
          else{
            pic_antar = '';
          }

          body += '<tr '+pic_antar+' id="tr+'+result.pengantaran[i].id+'">';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+count+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].kode_request+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].name+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].nam+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].gmc+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].description+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].quantity_request+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].quantity_check+'</td>';
          body += '<td onClick="countPicked(this)" id="'+result.pengantaran[i].id+'">'+result.pengantaran[i].area+'</td>';
          if(result.pengantaran[i].status_aktual == "proses1"){
            $('#btn_antars').show();
            body += '<td style="background-color: #ffea00;" id="td8+'+result.pengantaran[i].id+'">Belum diantar</td>';
            body += '<td onClick="countPicked(this)"><input type="checkbox" name="P" id="print_'+result.pengantaran[i].id+'"></td>';
          }else{
            $('#btn_antars').hide();            
            body += '<td style="background-color: #229905;" id="td8+'+result.pengantaran[i].id+'">Sedang diantar</td>';
            body += '<td onClick="countPicked(this)><input type="checkbox" name="P" id="print_'+result.pengantaran[i].id+'" hidden></td>';
          }
          count++;

          body += '</tr>';


        }
        $("#tableBodyResume1").append(body);

        // $('#tableResume tfoot th').each( function () {
        //   var title = $(this).text();
        //   $(this).html( '<input  style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
        // } );

        $('#tableResume').DataTable({
          
          'paging': false,
          'lengthChange': true,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bAutoWidth": true,
          "processing": true
        });


        // tableResume.columns().every( function () {
        //   var that = this;
        //   $( '#search', this.footer() ).on( 'keyup change', function () {
        //     if ( that.search() !== this.value ) {

        //       console.log(that.search());

        //       console.log(this.value);

        //       that
        //       .search( this.value )
        //       .draw();
        //     }
        //   } );
        // } );

        $('#tableResume tfoot tr').appendTo('#tableResume thead');

      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function checkAll(element){
    var id = $(element).attr("id");
    var checkVal = $('#'+id).is(":checked");

    console.log(checkVal);

    if(checkVal) {
      total = $('#total').text();
      $('input:checkbox').prop('checked', true);
    }else{
      total = 0;
      $('input:checkbox').prop('checked', false);
    }
    $("#picked").html(total);
  }

  function UpdatePengantaran(element){
    var tag = [];

    $("input[type=checkbox]:checked").each(function() {
      if (this.id.indexOf("All") >= 0) {

      } else {
        tag.push(this.id);
      }
    });
    if(tag.length < 1){
      alert("GMC Belum Dipilih");
      return false;
    }else{
      $('#scanModalLogout').modal('show');
      $('#scanner2').show();
      showCheckLogout();
      $('#tag_checkbox').val(tag);


    }
  }

  function fetchAll() {
    $.get('{{ url("fetch/lokasi/pengantaran") }}', function(result, status, xhr){
      if (result.status) {
        detail_all1 = [];
        for (var i = 0; i < result.cek_pen.length; i++) {
          detail_all1.push({id: result.cek_pen[i].id,kode_request:result.cek_pen[i].kode_request, area:result.cek_pen[i].area, area_code: result.cek_pen[i].area_code, lokasi_awal: result.cek_pen[i].lokasi_awal, gmc: result.cek_pen[i].gmc,description: result.cek_pen[i].description,lot: result.cek_pen[i].lot,quantity_request: result.cek_pen[i].quantity_request,quantity_check: result.cek_pen[i].quantity_check,no_hako: result.cek_pen[i].no_hako,name: result.cek_pen[i].name,oum: result.cek_pen[i].uom});
        }
      }
    });
  }


  function fetchCekLokasi() {
    $.get('{{ url("fetch/lokasi/pengantaran") }}', function(result, status, xhr){
      if (result.status) {

        var cek = result.cek_lokasi.length;
        var ces = result.cek_pengantaran.length;

        cek_all.push({id: cek});

        console.log(cek);
        console.log(ces);


        if (cek_all[0].id < 1 && ces < 1 ){
          window.location.href = "{{secure_url('index/joblist/operator')}}";

        }else{

          $('#cek_lokasi').DataTable().clear();
          $('#cek_lokasi').DataTable().destroy();
          $('#cek_lokasi_body').html("");

          detail_all = [];

          var body = '';
          var css = 'style="background-color: #000000;"';
          count=1

          for (var i = 0; i < result.cek_lokasi.length; i++) {

            detail_all.push({kode_request:result.cek_lokasi[i].kode_request, area:result.cek_lokasi[i].area, area_code: result.cek_lokasi[i].area_code, lokasi_awal: result.cek_lokasi[i].lokasi_awal});

            body += '<tr id="tr+'+result.cek_lokasi[i].id+'">';
            body += '<td >'+result.cek_lokasi[i].kode_request+'</td>';
            body += '<td >'+result.cek_lokasi[i].lokasi_awal+'</td>'; 
            body += '<td >'+result.cek_lokasi[i].area+'</td>';
            body += '<td >'+result.cek_lokasi[i].total_material+'</td>'; 
            body += '<td style="padding:0;" id="butview_'+count+'"> <a class="btn btn-success btn-xs" id="kode" onclick="detail_antar('+result.cek_lokasi[i].kode_request+')" style="border-color: green;"><i class="fa fa-eye"></i> Detail</a></td>';
            body += '<td id="tdscan_'+count+'">'+ "<a id='btnscan_"+count+"' onclick='check(\""+result.cek_lokasi[i].kode_request+"\",this.id,\""+result.cek_lokasi[i].area_code+"\",\""+result.cek_lokasi[i].area+"\");' class='btn btn-info btn-xs'><i class='fa fa-camera-retro'></i> Scan</a>" +'</td>';
            count++;

            body += '</tr>';

          }
          $("#cek_lokasi_body").append(body);

          $('#cek_lokasi tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input style="text-align: center;" class="cari" type="text" placeholder="Search '+title+'" />' );
          });
          store_detail = $('#cek_lokasi').DataTable({
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
                className: 'btn btn-default'
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
            },
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': false,
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
          });

          store_detail.columns().every( function () {
            var that = this;

            $( '.cari', this.footer() ).on( 'keyup change', function () {
              if ( that.search() !== this.value ) {
                that
                .search( this.value )
                .draw();
              }
            });
          });

          $('#store_detail tfoot tr').appendTo('#store_detail thead');
        }
      }
    });
}

function detail_antar(kode){
  $('#detail').val(kode);
  $('#modalDetail').modal('show');

  var data = {
    kode_request : $('#detail').val()
  }

    // $.get('{{ url("fetch/detail/pengantaran") }}',data, function(result, status, xhr){
    //   if(result.status){
      $('#detail_material').DataTable().clear();
      $('#detail_material').DataTable().destroy();
      $('#detail_material_body').html("");
      var tableData = "";
      var num=1;
      $.each(detail_all1, function(key, value) {
        if (value.kode_request == kode) {
          tableData += '<tr>';
          tableData += '<td>'+ value.kode_request +'</td>';
          tableData += '<td>'+value.name+'</td>';
          tableData += '<td>'+ value.no_hako +'</td>';
          tableData += '<td>'+ value.gmc +'</td>';
          tableData += '<td>'+ value.description +'</td>';
          tableData += '<td>'+ value.lot +'</td>';
          tableData += '<td>'+ value.oum +'</td>';
          tableData += '<td>'+ value.quantity_check +'</td>';
          tableData += '</tr>';
          num += 1;
        }
      });

      $('#detail_material_body').append(tableData);

      var table = $('#detail_material').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 7, 25, 50, -1 ],
        [ '7 rows', '25 rows', '50 rows', 'Show all' ]
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
          }
          ]
        },
        'paging': true,
        'lengthChange': true,
        'pageLength': 7,
        'searching': true ,
        'ordering': true,
        'order': [],
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true
      });
      // }
      // else{
      //   openErrorGritter('Error!', result.message);
      // }
    // });

  }



  function check(kode_request,id,lok,areas){
    $('#modalScan').modal('show');
    $("#loc").val(kode_request);;
    $("#lok_prod").val(lok);
    $("#area_request").val(areas);
    $("#id_button").val(id);
    showCheck();
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