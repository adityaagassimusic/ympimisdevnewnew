@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print">

<style type="text/css">
  table.table-bordered {
      border: 1px solid black;
  }

  table.table-bordered>thead>tr>th {
      border: 1px solid black;
      vertical-align: middle;
      text-align: center;
      padding-top: 5px;
      padding-bottom: 5px;
  }

  table.table-bordered>tbody>tr>td {
      border: 1px solid rgb(211, 211, 211);
     /* padding-top: 3px;
      padding-bottom: 3px;
      padding-left: 2px;
      padding-right: 2px;*/
      vertical-align: middle;
  }

  table.table-bordered>tfoot>tr>th {
      border: 1px solid rgb(211, 211, 211);
      vertical-align: middle;
  }

  #tableLive>tr:hover {
      background-color: #7dfa8c;
  }

  #tableLiveClose>tr:hover {
      background-color: #7dfa8c;
  }

  #tableLivePenjelasan>tr:hover {
      background-color: #7dfa8c;
  }

  #loading,
  #error {
      display: none;
  }

 .content-wrapper{
  background-color: #ecf0f5 !important;
 }

 .control-label{
  text-align: left !important;
 }

 .content-wrapper{
  background-color: ;
 }

 p > img{
  max-width: 300px;
  height: auto !important;
}

@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
  } /*End of Zoom in Keyframes */

  /* Zoom out Keyframes */
  @-webkit-keyframes zoomout {
    0% {transform: scale(0);}
    50% {transform: scale(0.5);}
    100% {transform: scale(0);}
  }
  @keyframes zoomout {
    0% {transform: scale(0);}
    50% {transform: scale(0.5);}
    100% {transform: scale(0);}
    }/*End of Zoom out Keyframes */


    #loading, #error { display: none; }

  </style>
  @endsection
  @section('header')
  <section class="content-header">
    <ol class="breadcrumb" id="last_update">
    </ol>
  </section>
  @endsection

  @section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <section class="content" style="padding-top: 0; padding-bottom: 0">

    <div id="loading"
        style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: white; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>

    <div class="row">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="col-xs-12"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text">LIVE (Listening Voice Of Employee)</span></center></div>

          <?php if(Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-GA" || Auth::user()->role_code == "C-GA" || Auth::user()->role_code == "S-HR" || Auth::user()->role_code == "C-HR" || Auth::user()->role_code == "M-HR" || Auth::user()->role_code == "D") { ?>

          <div class="col-md-12" style="padding-top: 20px">

            <div class="col-xs-2 pull-right">

                <button class="btn btn-success" onclick="addAspirasi()" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-plus"></i> Add Aspirasi Karyawan</button>
            </div>
            

          </div>

        <?php } ?>

<!--

      <form method="GET" action="{{ url("export/patrol/list") }}">
        <div class="col-xs-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" id="status" name="status" data-placeholder="Pilih Status" style="border-color: #605ca8">
                <option value=""></option>
                <option value="Open">Open</option>
                <option value="Penjelasan">Diberi Penjelasan</option>
                <option value="Close">Close</option>
            </select>
          </div>
        </div>
        <div class="col-xs-2">
          <button type="submit" class="btn btn-success form-control" style="width: 100%"><i class="fa fa-download"></i> Download Data</button>
        </div> 
      </form>
    -->

      <div class="col-md-12" style="padding: 1px !important;margin-top: 10px;">
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From"onchange="drawChart()">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To" onchange="drawChart()">
          </div>
        </div>
        

        </div>
      
      <!-- <div class="col-md-12" style="padding-top: 10px;">
        <div id="chart" style="width: 99%; height: 300px;"></div>
      </div> -->


        <div class="col-md-6" style="padding-top:10px">
          <div id="chart_kategori" style="width: 99%; height: 300px;"></div>
        </div>

        <div class="col-md-6" style="padding-top: 10px;">
          <div id="chart_bulan" style="width: 99%; height: 300px;"></div>
        </div>

        <div class="col-xs-12">
          <div class="row">
            <hr style="border: 1px solid red;background-color: red">
          </div>
        </div>

        <div class="col-xs-12">
          <div class="nav-tabs-custom" style="margin-top: 1%;">
              <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                  <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">LIVE Open</a>
                  </li>
                  <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">LIVE Close</a>
                  </li>
                  <li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">LIVE Diberi Penjelasan</a>
                  </li>
              </ul>
              <div class="tab-content">
                  <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                      <table id="tableLive" class="table table-bordered" style="width: 100%;">
                          <thead style="background-color: rgba(126,86,134,.7);">
                              <tr>
                                  <th style="width:2%;">Tanggal</th>
                                  <th style="width:2%;">Jenis</th>
                                  <th style="width:2%;">Bagian</th>
                                  <th style="width:10%;">Note</th>
                                  <th style="width:3%;">Status</th>
                                  <th style="width:5%;">Follow Up</th>
                              </tr>
                          </thead>
                          <tbody id="tableLiveBody" style="vertical-align: middle; text-align: center;">
                              
                          </tbody>
                              <!-- <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr> -->
                      </table>
                  </div>

                  <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                      <table id="tableLiveClose" class="table table-bordered"
                          style="width: 100%; ">
                          <thead style="background-color: rgba(126,86,134,.7);">
                              <tr>
                                  <th style="width:2%;">Tanggal</th>
                                  <th style="width:2%;">Jenis</th>
                                  <th style="width:2%;">Bagian</th>
                                  <th style="width:10%;">Note</th>
                                  <th style="width:3%;">Status</th>
                                  <th style="width:5%;">Follow Up</th>
                              </tr>
                          </thead>
                          <tbody id="tableLiveCloseBody" style="vertical-align: middle; text-align: center;">
                              <tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                              </tr>
                          </tbody>
                      </table>
                  </div>

                  <div class="tab-pane" id="tab_3" style="overflow-x: auto;">
                      <table id="tableLivePenjelasan" class="table table-bordered"
                          style="width: 100%; ">
                          <thead style="background-color: rgba(126,86,134,.7);">
                              <tr>
                                  <th style="width:2%;">Tanggal</th>
                                  <th style="width:2%;">Jenis</th>
                                  <th style="width:2%;">Bagian</th>
                                  <th style="width:10%;">Note</th>
                                  <th style="width:3%;">Status</th>
                                  <th style="width:5%;">Follow Up</th>
                              </tr>
                          </thead>
                          <tbody id="tableLivePenjelasanBody" style="vertical-align: middle; text-align: center;">
                              <tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
      
<!--       <div class="col-md-12" style="">
        <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
          <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
            <tr>
              <th style="width: 3%; vertical-align: middle;;font-size: 16px;">Kategori Audit</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Tanggal</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Lokasi</th>
              <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Auditor</th>
              <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Auditee</th>
              <th style="width: 10%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Note</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Status</th>
              <th style="width: 4%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Penanganan</th>
            </tr>
          </thead>
          <tbody id="tabelisi">
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div> -->

  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Auditor</th>
                    <th>Auditee</th>
                    <th>Foto</th>
                    <th>Penanganan</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModalCategory">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_category"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example3" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Note</th>
                    <th>Penanganan</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModalBulan">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_bulan"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example4" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Kategori</th>
                    <th>Note</th>
                    <th>Penanganan</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEdit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Edit Temuan Audit</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-12">
                  <label for="tanggal_edit">Tanggal</label>
                  : <span name="tanggal_edit" id="tanggal_edit"> </span>
                </div>
                <div class="col-md-12">
                  <label for="lokasi_edit">Lokasi</label>
                  : <span name="lokasi_edit" id="lokasi_edit"> </span>
                </div>
                <div class="col-md-12">
                  <label for="poin_edit">Kategori Patrol</label>
                  : <span name="poin_edit" id="poin_edit"> </span>
                </div>
                <div class="col-md-12">
                  <label for="pic_edit">PIC</label>
                  : <span name="pic_edit" id="pic_edit"> </span>
                </div>
                <div class="col-md-12">
                  <label for="note_edit">Note</label>
                  <textarea class="form-control" id="note_edit" name="note_edit"></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12">
                  <label for="image_edit">Temuan</label>
                  : <div name="image_edit" id="image_edit"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan_edit">
          <button type="button" onclick="post_edit()" class="btn btn-success" data-dismiss="modal"><i class="fa fa-pencil"></i> Update Audit</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPenanganan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Detail Temuan Audit</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-5">
                <div class="col-md-12">
                  <label for="lokasi">Lokasi</label>
                  : <span name="lokasi" id="lokasi"> </span>
                </div>
                <div class="col-md-12">
                  <label for="tanggal">Tanggal</label>
                  : <span name="tanggal" id="tanggal"> </span>
                </div>
                <div class="col-md-12">
                  <label for="note">Note</label>
                  : <span name="note" id="note"> </span>
                </div>
              </div>
              <div class="col-md-7">
                <h4>Catatan Penanganan</h4>
                <textarea class="form-control" required="" name="penanganan" id="penanganan" style="height: 100px;"></textarea> 
                <h4>Bukti Penanganan</h4>
                <input type="file" required="" id="bukti_penanganan" name="bukti_penanganan" accept="image/*" capture="environment">
                <h4>Status Penanganan</h4>
                <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_penjelasan" onclick="btnAction('penjelasan')" class="btn btn-md">Sudah Dijelaskan</a>
                <a href="javascript:void(0)" style="border-color: black; color: black;" id="btn_close" onclick="btnAction('close')" class="btn btn-md">Sudah Difollow Up</a>
                <input type="hidden" id="btn_status" name="btn_status" required="">
              </div>

              
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan">
          <button type="button" onclick="update_penanganan()" class="btn btn-success"><i class="fa fa-pencil"></i> Submit Penanganan</button>
        </div>
      </div>
    </div>
  </div>


<div class="modal fade" id="modalAspirasi">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <center style="background-color: green">
          <span style="font-weight: bold; font-size: 1.5vw;color: black">Aspirasi Karyawan</span>
        </center>
        <hr>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
          <form class="form-horizontal">
            <div class="col-xs-12" style="padding-bottom: 5px;">
              <div class="form-group">
                <label for="addJenis" class="col-sm-12 control-label">Jenis Aspirasi<span class="text-red">*</span></label>
                <div class="col-sm-12">
                  <select class="form-control select5" style="width: 100%" id="addJenis" data-placeholder="Pilih Jenis Aspirasi">
                    <option value=""></option>
                    <option value="Fasilitas Karyawan">Fasilitas Karyawan</option>
                    <option value="Kegiatan Karyawan">Kegiatan Karyawan</option>
                    <option value="Peraturan">Peraturan</option>
                    <option value="Personal">Personal</option>
                    <option value="Safety">Safety</option>
                    <option value="Training">Training</option>
                    <option value="Digitalisasi">Digitalisasi</option>
                    <option value="Lain-lain">Lain-lain</option>
                  </select>
                </div>
              </div>
               <div class="form-group">
                <label for="addBagian" class="col-sm-12 control-label">Bagian<span class="text-red">*</span></label>
                <div class="col-sm-12">
                  <select class="form-control select5" style="width: 100%" id="addBagian" data-placeholder="Pilih Bagian">
                    <option value=""></option>
                    @foreach($location as $loc)
                    <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label for="addNote" class="col-sm-12 control-label">Catatan Permasalahan<span class="text-red">*</span></label>
                <div class="col-sm-12">
                  <textarea id="addNote" height="100%" class="form-control note"></textarea>
                </div>
              </div>

            </div>
          </form>
        </div>
        <div class="box-footer">
          <div class="col-xs-12">
            <a class="btn btn-success pull-right" onclick="save()" style="font-size: 1.2vw; font-weight: bold;" id="save">Simpan</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</section>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/data.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('.select2').select2();
    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });


  function addAspirasi(){
    // $('#loading').show();
      clearAll();
      $('#loading').hide();
      $("#save").show();
      $('#modalAspirasi').modal('show');
      return false;
  }

  $(function () {

    $('.select5').select2({
      allowClear:true
    });
  })


  function clearAll(){
    $('#addJenis').val('');
    $('#addBagian').val('');
    $('#addNote').val('');
  }

  function drawChart() {    
    fetchTable();

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status,
    };

    $.get('{{ url("fetch/live/monitoring") }}', data, function(result, status, xhr) {
      if(result.status){

        var tgl = [];

        var kategori = [];
        var belum_ditangani = [];
        var sudah_dijelaskan = [];
        var sudah_ditangani = [];

        var bulan = [];
        var tahun = [];
        var belum_ditangani_bulan = [];
        var sudah_ditangani_bulan = [];
        var sudah_dijelaskan_bulan = [];

        $.each(result.data_kategori, function(key, value) {
          kategori.push(value.point_judul);
          belum_ditangani.push(parseInt(value.jumlah_belum));
          sudah_dijelaskan.push(parseInt(value.jumlah_penjelasan));
          sudah_ditangani.push(parseInt(value.jumlah_sudah));
        });

        $.each(result.data_bulan, function(key, value) {
          bulan.push(value.bulan);
          tahun.push(value.tahun);
          belum_ditangani_bulan.push({y: parseInt(value.jumlah_belum),key:value.tahun});
          sudah_dijelaskan_bulan.push({y: parseInt(value.jumlah_penjelasan),key:value.tahun});
          sudah_ditangani_bulan.push({y: parseInt(value.jumlah_sudah),key:value.tahun});
        });

        Highcharts.chart('chart_kategori', {
          chart: {
              backgroundColor: null,
              type: 'column',

          },
          title: {
            text: "Resume Aspirasi LIVE",
            style: {
                fontWeight: 'bold',
                color: 'Black'
            }
          },
          credits: {
              enabled: false
          },
          xAxis: {
              tickInterval: 1,
              gridLineWidth: 1,
              categories: kategori,
              crosshair: true
          },
          yAxis: [{
              title: {
                  text: 'Jumlah',
                  style: {
                      fontWeight: 'bold',
                  },
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      fontSize: '0.8vw'
                  }
              },
          }],
          exporting: {
              enabled: false
          },
          legend: {
              enabled: true,
              borderWidth: 1
          },
          tooltip: {
              enabled: true
          },
          plotOptions: {
              column: {
                  stacking: 'normal',
                  pointPadding: 0.93,
                  groupPadding: 0.93,
                  borderWidth: 0.8,
                  borderColor: 'black'
              },
              series: {
                  dataLabels: {
                      enabled: true,
                      formatter: function() {
                          return (this.y != 0) ? this.y : "";
                      },
                      style: {
                          textOutline: false
                      }
                  },
                  cursor: 'pointer',
                  point: {
                      events: {
                          click: function() {
                            ShowModalCategory(this.category,this.series.name);
                          }
                      }
                  }
              }
          },
          series: [{
              name: 'Belum Ditangani',
              data: belum_ditangani,
              color: '#feccfe'
          }, {
              name: 'Sudah Dijelaskan',
              data: sudah_dijelaskan,
              color: '#ccffff'
          }, {
              name: 'Sudah Ditangani',
              data: sudah_ditangani,
              color: 'rgb(34, 204, 125)'
          }]
      });


        $('#chart_bulan').highcharts({
          chart: {
            type: 'column',
            backgroundColor: null
          },
          title: {
            text: "Resume Aspirasi LIVE Per Bulan",
            style: {
                fontWeight: 'bold',
                color: 'Black'
            }
          },
          credits: {
              enabled: false
          },
          xAxis: {
              tickInterval: 1,
              gridLineWidth: 1,
              categories: bulan,
              crosshair: true
          },
          yAxis: [{
              title: {
                  text: 'Jumlah',
                  style: {
                      fontWeight: 'bold',
                  },
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      fontSize: '0.8vw'
                  }
              },
          }],
          exporting: {
              enabled: false
          },
          legend: {
              enabled: true,
              borderWidth: 1
          },
          tooltip: {
              enabled: true
          },
          plotOptions: {
              column: {
                  stacking: 'normal',
                  pointPadding: 0.93,
                  groupPadding: 0.93,
                  borderWidth: 0.8,
                  borderColor: 'black'
              },
              series: {
                  dataLabels: {
                      enabled: true,
                      formatter: function() {
                          return (this.y != 0) ? this.y : "";
                      },
                      style: {
                          textOutline: false
                      }
                  },
                  cursor: 'pointer',
                  point: {
                      events: {
                          click: function() {
                            ShowModalBulan(this.category,this.series.name);
                          }
                      }
                  }
              }
          },

          tooltip: {
            formatter:function(){
              return this.series.name+' : ' + this.y;
            }
          },
          series: [{
              name: 'Belum Ditangani',
              data: belum_ditangani_bulan,
              color: '#feccfe'
          }, {
              name: 'Sudah Dijelaskan',
              data: sudah_dijelaskan_bulan,
              color: '#ccffff'
          }, {
              name: 'Sudah Ditangani',
              data: sudah_ditangani_bulan,
              color: 'rgb(34, 204, 125)'
          }
          ]
        })
      } else{
        alert('Attempt to retrieve data failed');
      }
    })
  }



  function ShowModalCategory(kategori, status) {
    tabel = $('#example3').DataTable();
    tabel.destroy();

    $("#myModalCategory").modal("show");

    var table = $('#example3').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("index/live/detail_category") }}",
        "data" : {
          kategori : kategori,
          status : status
        }
      },
      "columns": [
      {"data": "tanggal", "width": "15%"},
      {"data": "lokasi" , "width": "15%"},
      {"data": "point_judul", "width": "20%"},
      {"data": "note", "width": "20%"},
      {"data": "penanganan", "width": "20%"},
      {"data": "status", "width": "10%"}
      ]    
    });

    $('#judul_table_category').append().empty();
    $('#judul_table_category').append('<center><b>LIVE Kategori '+kategori+'</b></center>'); 
  }


  function ShowModalBulan(bulan, status) {
    tabel = $('#example4').DataTable();
    tabel.destroy();

    $("#myModalBulan").modal("show");

    var table = $('#example4').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("index/live/detail_bulan") }}",
        "data" : {
          bulan : bulan,
          status : status
        }
      },
      "columns": [
      {"data": "tanggal", "width": "15%"},
      {"data": "lokasi" , "width": "15%"},
      {"data": "point_judul", "width": "20%"},
      {"data": "note", "width": "30%"},
      {"data": "penanganan", "width": "20%"},
      {"data": "status", "width": "10%"}
      ]    
    });

    $('#judul_table_bulan').append().empty();
    $('#judul_table_bulan').append('<center><b>Patrol Bulan '+bulan+' '+status+'</b></center>'); 
  }



  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status
    };

    $.get('{{ url("index/live/table") }}', data, function(result, status, xhr){
      if(result.status){

        $('#tableLive').DataTable().clear();
        $('#tableLive').DataTable().destroy();

        $('#tableLiveClose').DataTable().clear();
        $('#tableLiveClose').DataTable().destroy();

        $('#tableLivePenjelasan').DataTable().clear();
        $('#tableLivePenjelasan').DataTable().destroy();

        $("#tableLiveBody").find("td").remove();  
        $('#tableLiveBody').html("");

        $("#tableLiveCloseBody").find("td").remove();  
        $('#tableLiveCloseBody').html("");

        $("#tableLivePenjelasanBody").find("td").remove();  
        $('#tableLivePenjelasanBody').html("");

        var table = "";
        var table_close = "";
        var table_penjelasan = "";

        $.each(result.datas, function(key, value) {

          table += '<tr>';
          table += '<td style="text-align: center;">'+getFormattedDate(new Date(value.tanggal))+'</span></td>';
          table += '<td>'+value.point_judul+'</td>';
          table += '<td>'+value.lokasi+'</td>';
          table += '<td>'+value.note+'</td>';
          
          table += '<td style="text-align:center;font-size:16px"><span class="label label-danger">Open</span></td>';

            table += '<td style="text-align: center;">';
            // table += '<button style="height: 100%; margin-right: 5px;" onclick="edit(\''+value.id+'\')" class="btn btn-md btn-primary form-control"><i class="fa fa-pencil-square-o"></i> Edit</button>';
            if ("{{ Auth::user()->role_code }}" == "S-MIS" || "{{ Auth::user()->role_code }}" == "S-GA" || "{{ Auth::user()->role_code }}" == "S-HR" || "{{ Auth::user()->role_code }}" == "C-GA" || "{{ Auth::user()->role_code }}" == "C-HR" || "{{ Auth::user()->role_code }}" == "M-HR" || "{{ Auth::user()->role_code }}" == "D") {
            table += '<button style="height: 100%;" onclick="penanganan(\''+value.id+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-thumbs-o-up"></i> Penanganan</button>';
            }
            table += '</td>';
            table += '</tr>';
          })

         $.each(result.data_close, function(key, value) {

          table_close += '<tr>';
          table_close += '<td style="text-align: center;">'+getFormattedDate(new Date(value.tanggal))+'</span></td>';
          table_close += '<td>'+value.point_judul+'</td>';
          table_close += '<td>'+value.lokasi+'</td>';
          table_close += '<td>'+value.note+'</td>';
          
          table_close += '<td style="text-align:center;font-size:16px"><span class="label label-success">Close</span></td>';

            table_close += '<td style="text-align: center;">';
            table_close += '<span class="label label-success">Sudah Difollow Up</span>';
            table_close += '</td>';
            table_close += '</tr>';
          })

         $.each(result.data_penjelasan, function(key, value) {

            table_penjelasan += '<tr>';
            table_penjelasan += '<td style="text-align: center;">'+getFormattedDate(new Date(value.tanggal))+'</span></td>';
            table_penjelasan += '<td>'+value.point_judul+'</td>';
            table_penjelasan += '<td>'+value.lokasi+'</td>';
            table_penjelasan += '<td>'+value.note+'</td>';
            
            table_penjelasan += '<td style="text-align:center;font-size:16px"><span class="label label-success">Close</span></td>';
            table_penjelasan += '<td style="text-align: center;">';
            table_penjelasan += '<span class="label label-info">Sudah Diberi Penjelasan</span>';
            table_penjelasan += '</td>';
            table_penjelasan += '</tr>';
          })

        $('#tableLiveBody').append(table);
        $('#tableLiveCloseBody').append(table_close);
        $('#tableLivePenjelasanBody').append(table_penjelasan);

        $('#tableLive').DataTable({
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
          },
          'responsive':true,
          'paging': true,
          'lengthChange': false,
          'pageLength': 10,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': false,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        $('#tableLiveClose').DataTable({
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
          },
          'responsive':true,
          'paging': true,
          'lengthChange': false,
          'pageLength': 10,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': false,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        $('#tableLivePenjelasan').DataTable({
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
          },
          'responsive':true,
          'paging': true,
          'lengthChange': false,
          'pageLength': 10,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': false,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });
      }
    })
  }

  function penanganan(id) {

    $('#modalPenanganan').modal("show");
    $("#penanganan").val("");
    $("#bukti_penanganan").val("");
    $("#btn_close").css('background-color', '');
    $("#btn_penjelasan").css('background-color', '');
    $("#btn_status").val("");

    var data = {
      id : id
    }

    $.get('{{ url("index/live/detail_penanganan") }}', data, function(result, status, xhr){
      if (result.status) {
        $("#id_penanganan").val(id);
        $("#lokasi").text(result.audit[0].lokasi);
        $("#tanggal").text(result.audit[0].tanggal);
        $("#note").text(result.audit[0].note);
      } else {
        openErrorGritter('Error');
      }

    }); 
  }

  function update_penanganan() {

    // if ($("#penanganan").val() == "") {
    //   openErrorGritter("Error","Catatan Penanganan Harus Diisi");
    //   return false;
    // }

    // if ($("#bukti_penanganan").val() == "") {
    //   openErrorGritter("Error","Bukti Penanganan Harus Diisi");
    //   return false;
    // }

    if ($("#btn_status").val() == "") {
      openErrorGritter("Error","Status Penanganan Harus Diisi");
      return false;
    }


    var formData = new FormData();
    formData.append('id', $("#id_penanganan").val());
    formData.append('penanganan', $("#penanganan").val());
    formData.append('bukti_penanganan', $('#bukti_penanganan').prop('files')[0]);
    formData.append('btn_status', $("#btn_status").val());

    // var data = {
    //   id: $("#id_penanganan").val(),
    //   penanganan : CKEDITOR.instances.penanganan.getData()
    // };


    // $.post('{{ url("post/audit_patrol/penanganan") }}', data, function(result, status, xhr){
    //   if (result.status == true) {
    //     openSuccessGritter("Success","Audit Berhasil Ditangani");
    //     fetchTable();
    //     drawChart();
    //   } else {
    //     openErrorGritter("Error",result.datas);
    //   }
    // })

    $.ajax({
      url:"{{ url('post/live/penanganan') }}",
      method:"POST",
      data:formData,
      dataType:'JSON',
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        openSuccessGritter("Success","Status LIVE Berhasil Diperbarui");
        $('#modalPenanganan').modal("hide");
        fetchTable();
        drawChart();
      },
      error: function (response) {
        openErrorGritter("Error",result.datas);
        $('#modalPenanganan').modal("hide");
      },
    });

  }


  function save(){
    $('#loading').show();
    var point_judul = $('#addJenis').val();
    var lokasi = $('#addBagian').val();
    var note = $('#addNote').val();

    var data = {
      point_judul:point_judul,
      lokasi:lokasi,
      note:note
    }

    if(point_judul != '' && lokasi != '' && note != ""){
      $.post('{{ url("post/live") }}', data, function(result, status, xhr){
        if(result.status){
          $('#modalAspirasi').modal('hide');
          openSuccessGritter('Success!', result.message);
          clearAll();
          drawChart();
          $('#loading').hide();
        }
        else{
          openErrorGritter('Error!', result.message);
          $('#loading').hide();
        }
      });
    }
    else{
      openErrorGritter('Error!', 'Data harus lengkap tidak boleh ada yang kosong');
      $('#loading').hide();   
    }

  }

  function btnAction(cat){
    $('#btn_penjelasan').css('background-color', 'white');
    $('#btn_close').css('background-color', 'white');

    if (cat == "close") {
      $('#btn_'+cat).css('background-color', '#90ed7d');
    }else{
      $('#btn_'+cat).css('background-color', '#f39c12');
    }
    $('#btn_status').val(cat);
  }

  function edit(id) {

    $('#modalEdit').modal("show");

    var data = {
      id : id
    }

    $.get('{{ url("index/audit_patrol/detail_penanganan") }}', data, function(result, status, xhr){

      var images_edit = "";
      $("#image_edit").html("");

      if (result.status) {
        $("#id_penanganan_edit").val(id);
        $("#tanggal_edit").text(result.audit[0].tanggal);
        $("#lokasi_edit").text(result.audit[0].lokasi);
        $("#poin_edit").text(result.audit[0].point_judul);
        $("#pic_edit").text(result.audit[0].auditee_name);
        $("#note_edit").val(result.audit[0].note);

        images_edit += '<img src="{{ url("files/patrol") }}/'+result.audit[0].foto+'" width="300">';
        $("#image_edit").append(images_edit);

      } else {
        openErrorGritter('Error');
      }

    }); 
  }


  function post_edit() {

    var data = {
      id: $("#id_penanganan_edit").val(),
      note : $("#note_edit").val(),
    };

    $.post('{{ url("post/audit_patrol/edit") }}', data, function(result, status, xhr){
      if (result.status == true) {
        openSuccessGritter("Success","Audit Berhasil Diedit");
        fetchTable();
      } else {
        openErrorGritter("Error",result.datas);
      }
    })
  }


  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
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
  @stop