@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    font-size: 13px;
    text-align: center;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
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
  #loading, #error { display: none; }
  
</style>
@endsection

@section('header')
@stop

@section('content')


<section class="content" style="padding-top:0">


  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
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
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="col-xs-12" style="padding:0">

    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
      <div class="box-body">
        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
          <div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
            <span style="font-size: 25px;color: black;width: 25%;">FORM YOKOTENKAI <?= strtoupper($emp_dept->department) ?></span>
          </div>
          <div class="col-xs-5" style="padding:0;margin-top:10px">
            <div class="form-group">
              <label id="label_section">Lokasi Kejadian</span></label>
              <input type="text" class="form-control" value="{{$accident->location}} - {{$accident->area}}" readonly>
            </div>
          </div>
          <div class="col-xs-2" style="margin-top:10px">
            <div class="form-group">
              <label id="label_section">Tanggal Kejadian</span></label>
              <input type="text" class="form-control" value="<?php echo date('d-M-Y', strtotime($accident->date_incident)) ?> {{$accident->time_incident}}" readonly>
            </div>
          </div>
          <div class="col-xs-5" style="margin-top:10px">
            <div class="form-group">
              <label id="label_section">Kondisi Korban</span></label>
              <input type="text" class="form-control" value="{{$accident->condition}}" readonly>
            </div>
          </div>
          <div class="col-xs-5" style="padding:0">
            <div class="form-group">
              <label id="label_section">Detail Kejadian</span></label>
              <textarea class="form-control" readonly style="height: 150px">{{$accident->detail_incident}}</textarea>
            </div>
          </div>
          <?php 
          $data_image = json_decode($accident->illustration_image);
          $data_detail = json_decode($accident->illustration_detail);
          $jumlah = count($data_image);
          ?>

          <div class="col-xs-7">
            <div class="form-group">
                <label id="label_section">Illustrasi Kejadian</span></label>
                <br>
                <?php
                  for ($i = 0; $i < $jumlah; $i++) { ?>
                      <div style="display: inline-block;vertical-align: middle;">
                      <img src="{{ url('files/kecelakaan/kecelakaan_kerja/'.$data_image[$i])}}"  height="130"> 
                      <br>
                      <b><?= $i+1 ?>. <?= $data_detail[$i] ?> </b>
                    </div>
                  <?php } ?>    
            </div>
          </div>

          <div class="col-xs-12" style="padding:0">
            <div class="form-group">
              <label id="label_section">Informasi Yokotenkai Dari Standarisasi</span></label>
              <?= $accident->yokotenkai ?>
              <!-- <textarea class="form-control" readonly style="height: 150px"></textarea> -->
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- <div class="col-xs-12" style="padding:0">
    <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
        <div class="box-body">
          <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
            <div class="col-md-12" style="padding:0;text-align: left;">
              <b>Klik Untuk Melakukan Training / Sosialiasi Ke Operator</b>
              <br><br>
              <a href="javascript:void(0)" data-toggle="modal" class="btn btn-md btn-success" onClick="sosialisasi({{$id}})"><i class="fa fa-users"></i> Sosialisasi</a>
              <br>

              <span style="text-align:right">(Jumlah Sudah Disosialiasi Pada Yokotenkai ini : <span id="jumlah_sosialiasi"></span> Orang)</span>
            </div>
          </div>
        </div>
      </div>
  </div> -->

  <div class="col-xs-12" style="padding:0">
    <form id ="yokotenkai_form" name="yokotenkai_form" method="post" action="{{ url('post/yokotenkai/'.$id) }}" enctype="multipart/form-data">
    <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
        <div class="box-body">
          <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
           
            <div class="col-md-4" style="padding:0">
              <div class="form-group">
                <label id="label_section">PIC Pengecekan<span class="text-red">*</span></label>
                <input type="text" class="form-control" id="nik" name="nik" value="{{$emp_dept->employee_id}} - {{$emp_dept->name}}" readonly>
                <input type="hidden" class="form-control" id="employee_id" name="employee_id" value="{{$emp_dept->employee_id}} - {{$emp_dept->name}}" readonly>
                <input type="hidden" class="form-control" id="employee_name" name="employee_name" value="{{$emp_dept->name}}" readonly>
                <input type="hidden" class="form-control" id="department" name="department" value="{{$emp_dept->department}}" readonly>
                <input type="hidden" class="form-control" id="jumlah_grup" name="jumlah_grup" value="<?= count($group) ?>">
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <label>Klik Untuk Melakukan Training / Sosialiasi Ke Operator</label>
                <br>
                <a href="javascript:void(0)" style="width:100%" data-toggle="modal" class="btn btn-md btn-success" onClick="sosialisasi({{$id}})"><i class="fa fa-users"></i> Sosialisasi</a>
                <!-- <a class="btn btn-md btn-warning" onclick="ShowChart({{$id}})"><i class="fa fa-bar-chart"></i> Chart</a> -->
                <br>
                <span class="pull-left" style="text-align:right;">
                Jumlah Sudah Disosialiasi Pada Yokotenkai ini : <span id="jumlah_sosialiasi" style="color:red;font-size: 20px"></span> </span>
              </div>
            </div>
            <div class="col-md-12" style="padding:0">
              <br>
              <div class="col-md-2" style="padding:0">
                  <label id="label_group">Grup<span class="text-red">*</span></label>
              </div>
              <div class="col-md-2">
                  <label id="label_group">Pekerjaan Serupa<span class="text-red">*</span></label>
              </div>
              <div class="col-md-2">
                  <label id="label_group">Peralatan/Mesin Sejenis<span class="text-red">*</span></label>
              </div>
              <div class="col-md-2">
                  <label id="label_group">Kesesuaian Standar K3<span class="text-red">*</span></label>
              </div>
              <!-- <div class="col-md-2">
                  <label id="label_group">Training/Sosialisasi<span class="text-red">*</span></label>
              </div> -->
              <div class="col-md-2">
                  <label id="label_group">Perlu Kaizen Atau Tidak<span class="text-red">*</span></label>
              </div>
              <div class="col-md-2">
                  <label id="label_group">Tanggal Pengecekan<span class="text-red">*</span></label>
              </div>
            </div>

            <?php for ($i=0; $i < count($group); $i++) { ?>
              <div class="col-md-12" style="padding:0">
                <div class="col-md-2" style="padding:0">
                  <div class="form-group">
                    <input type="text" class="form-control" id="group_<?= $i ?>" name="group_<?= $i ?>" value="{{ $group[$i]->group }}" readonly>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="form-control select2" id="pekerjaan_serupa_<?= $i ?>" name="pekerjaan_serupa_<?= $i ?>" data-placeholder="Pekerjaan Serupa" style="width: 100%;" onchange="pekerjaanSerupa(this)">
                      <option value="">&nbsp;</option>
                      <option value="Ada">Ada</option>
                      <option value="Tidak">Tidak</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <!-- <input type="text" class="form-control" id="peralatan_sejenis_<?= $i ?>" name="peralatan_sejenis_<?= $i ?>"> -->
                    <select class="form-control select2" id="peralatan_sejenis_<?= $i ?>" name="peralatan_sejenis_<?= $i ?>" data-placeholder="Peralatan Sejenis" style="width: 100%;" onchange="peralatanSejenis(this)">
                      <option value="">&nbsp;</option>
                      <option value="Ada">Ada</option>
                      <option value="Tidak">Tidak</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control" id="standar_k3_<?= $i ?>" name="standar_k3_<?= $i ?>" placeholder="Standar K3">
                  </div>
                </div>
                <!-- <div class="col-md-2">
                  <div class="form-group">
                    <select class="form-control select2" id="penerapan_<?= $i ?>" name="penerapan_<?= $i ?>" data-placeholder="Jenis Penerapan" style="width: 100%;"  onchange="training(this)" >
                      <option value="">&nbsp;</option>
                      <option value="Training">Training</option>
                      <option value="Sosialiasi">Sosialiasi</option>
                    </select>
                  </div>
                </div> -->
                <div class="col-md-2">
                  <div class="form-group">
                    <!-- <input type="text" class="form-control" id="kaizen" name="kaizen"> -->
                    <select class="form-control select2" id="kaizen_<?= $i ?>" name="kaizen_<?= $i ?>" data-placeholder="Kaizen" style="width: 100%;" onchange="kaizen(this)">
                      <option value="">&nbsp;</option>
                      <option value="Perlu Kaizen">Perlu Kaizen</option>
                      <option value="Tidak Perlu Kaizen">Tidak Perlu Kaizen</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right datepicker2" id="tanggal_pengecekan_<?= $i ?>" name="tanggal_pengecekan_<?= $i ?>" >
                      </div>
                  </div>
                </div>
              </div>

             <div class="col-md-6" id="pekerjaan_serupa_div_<?= $i ?>" style="display: none;">
                <div class="col-xs-12" style="background-color: #ffd8b7;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
                <span style="font-size: 20px;color: black;width: 25%;">Pekerjaan Serupa</span>
                </div>

                <div class="col-md-6" style="padding:0;padding-top:10px;padding-bottom:10px">
                  <textarea class="form-control" id="pekerjaan_serupa_detail_<?= $i ?>" name="pekerjaan_serupa_detail_<?= $i ?>"></textarea></td>
                </div>
                <div class="col-md-6" style="padding:0;padding-top:10px;text-align:center;">
                  <input type="file" onchange="readURL(this,'');" id="pekerjaan_serupa_file_<?= $i ?>" name="pekerjaan_serupa_file_<?= $i ?>" style="display:none" class="file">
                  <button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>
                  <img width="150px" id="blah" src="" style="display: none" alt="your image" />

                  <div id="pekerjaan_serupa_foto_<?= $i ?>"></div>
                </div>

              </div>

              <div class="col-md-6" id="peralatan_sejenis_div_<?= $i ?>" style="display: none;">
                <div class="col-xs-12" style="background-color: #ffd8b7;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
                <span style="font-size: 20px;color: black;width: 25%;">Peralatan / Mesin Sejenis</span>
                </div>

                <div class="col-md-6" style="padding:0;padding-top:10px;padding-bottom:10px">
                  <textarea class="form-control" id="peralatan_sejenis_detail_<?= $i ?>" name="peralatan_sejenis_detail_<?= $i ?>"></textarea></td>
                </div>
                <div class="col-md-6" style="padding:0;padding-top:10px;padding-bottom: 10px;text-align:center;">
                  <input type="file" onchange="readURL(this,'');" id="peralatan_sejenis_file_<?= $i ?>" name="peralatan_sejenis_file_<?= $i ?>" style="display:none" class="file">
                  <button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

                  <img width="150px" id="blah" src="" style="display: none" alt="your image" />

                  <div id="peralatan_sejenis_foto_<?= $i ?>"></div>
                </div>
              </div>

             <!--  <div class="col-md-6" id="penerapan_div_<?= $i ?>" style="display: none;">
                <div class="col-xs-12" style="background-color: #ffd8b7;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
                <span style="font-size: 20px;color: black;width: 25%;">Bukti Training/Sosialisasi</span>
                </div>

                <div class="col-md-12" style="padding:0;padding-top:10px;padding-bottom: 10px;text-align:center;">
                  <input type="file" onchange="readURL(this,'');" id="penerapan_file_<?= $i ?>" style="display:none" class="file">
                  <button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

                  <img width="150px" id="blah" src="" style="display: none" alt="your image" />
                </div>
              </div> -->

              <div class="col-md-6" id="kaizen_div_<?= $i ?>" style="display: none;">
                <div class="col-xs-12" style="background-color: #ffd8b7;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
                <span style="font-size: 20px;color: black;width: 25%;">Laporan Hasil Kaizen</span>
                </div>

                <div class="col-md-4" style="padding:0;padding-top:10px;padding-bottom: 10px;padding-bottom:10px">
                  <textarea class="form-control" id="kaizen_detail_<?= $i ?>" name="kaizen_detail_<?= $i ?>"></textarea></td>
                </div>

                <div class="col-md-4" style="padding:0;padding-top:10px;padding-bottom: 10px;text-align:center;">
                  <input type="file" onchange="readURL(this,'');" id="kaizen_before_<?= $i ?>" name="kaizen_before_<?= $i ?>" style="display:none" class="file">
                  <button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

                  <img width="150px" id="blah" src="" style="display: none" alt="your image" />

                  <div id="kaizen_sebelum_<?= $i ?>"></div>
                </div>

                <div class="col-md-4" style="padding:0;padding-top:10px;padding-bottom: 10px;text-align:center;">
                  <input type="file" onchange="readURL(this,'');" id="kaizen_after_<?= $i ?>" name="kaizen_after_<?= $i ?>" style="display:none" class="file">
                  <button type="button" class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

                  <img width="150px" id="blah" src="" style="display: none" alt="your image" />

                  <div id="kaizen_sesudah_<?= $i ?>"></div>
                </div>

              </div>

            <?php } ?>  
            </div>
            <div class="col-md-12" style="padding:0">
              <button id="kirim" class="btn btn-success" style="font-weight: bold; font-size: 25px; width: 100%;" type="submit">Save Data</button>
            </div>

            <?php if(str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'STD')) { ?>
            <div class="col-md-12" style="padding:0">    
              <br>   
              <a type="button" target="_blank" href="{{url('index/yokotenkai/pdf')}}/{{$id}}" class="btn btn-danger pull-right"><i class="fa fa-file-pdf-o"></i> Download Report PDF</a>
            </div>
          <?php } ?>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="modalDetailTitle"></h4>
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <center>
              <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
            </center>
            <form class="form-horizontal">
              <div class="col-xs-12">
                <input type="hidden" id="accident_id">
                <table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
                  <thead style="background-color: rgba(126,86,134,.7);">
                    <tr>
                      <th style="width: 20%; text-align: center; font-size: 1vw;">Total Employee</th>
                      <th style="width: 80%; text-align: center; font-size: 1vw;">Title</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td id="count_all" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
                      <td id="judul" style="text-align: center; font-size: 1.8vw; font-weight: bold;">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </form>
            <div class="col-xs-12">
              <div class="input-group" style="padding-bottom: 5px;">
                <input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan ID Card Here..." required>
                <div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
                  <i class="glyphicon glyphicon-credit-card"></i>
                </div>
              </div>
            </div>
            <div class="col-xs-12" style="padding-top: 10px;">
              <table class="table table-hover table-bordered table-striped" id="tableDetail">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%;">#</th>
                    <th style="width: 2%;">ID</th>
                    <th style="width: 4%;">Name</th>
                    <th style="width: 7%;">Dept</th>
                    <th style="width: 4%;">Attend Time</th>
                    <!-- <th style="width: 1%;">Action</th> -->
                  </tr>
                </thead>
                <tbody id="tableDetailBody">
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalChart" style="color: black;z-index: 10000;">
    <div class="modal-dialog modal-lg" style="width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
            <span aria-hidden="true">&times;</span>
          </button>
          
          <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_chart"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div id="container_sosialisasi" style="height: 40vh;"></div>
            </div> 

            <div class="col-xs-12">
              <input type="hidden" id="id_sosialiasi" name="id_sosialiasi">
              <a type="button" class="btn btn-info" style="width:100%" onclick="sosialiasi_kec()"><i class="fa fa-info-circle"></i> Scan Tap RFID</a>
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
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
  var no = 2;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('#tag').val('');
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      allowClear : true
    });
     fillForm();
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
   });

  $('.datepicker2').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
  });

  function pekerjaanSerupa(elem){
    var nomor = elem.id.split("_");

    if (elem.value == "Ada") {
      $('#pekerjaan_serupa_div_'+nomor[2]).show();      
    }else{
      $('#pekerjaan_serupa_div_'+nomor[2]).hide();   
    }
  }

  function peralatanSejenis(elem){
   var nomor = elem.id.split("_");

    if (elem.value == "Ada") {
      $('#peralatan_sejenis_div_'+nomor[2]).show();      
    }else{
      $('#peralatan_sejenis_div_'+nomor[2]).hide();   
    }
  }

  // function training(elem){
  //   var nomor = elem.id.split("_");
  //   if (elem.value == "") {
  //     $('#penerapan_div_'+nomor[1]).hide();      
  //   }else{
  //     $('#penerapan_div_'+nomor[1]).show();   
  //   }
  // }

  function kaizen(elem){
    var nomor = elem.id.split("_");
    if (elem.value == "Perlu Kaizen") {
      $('#kaizen_div_'+nomor[1]).show();      
    }else{
      $('#kaizen_div_'+nomor[1]).hide();   
    }
  }

  function fillForm() {
    var data = {
      id : '{{$id}}',
      dept : '{{$emp_dept->department}}'
    }
    
    $.get('{{ url("fetch/yokotenkai") }}', data, function(result, status, xhr){
      if(result.status){

        $('#jumlah_sosialiasi').text(result.yokotenkai_detail.jumlah+" Orang");

        for(var i = 0; i < result.yokotenkai.length; i++){
          $('#pekerjaan_serupa_'+i).val(result.yokotenkai[i].pekerjaan_serupa).trigger('change.select2');
          $('#peralatan_sejenis_'+i).val(result.yokotenkai[i].peralatan_sejenis).trigger('change');
          $('#standar_k3_'+i).val(result.yokotenkai[i].standar_k3);
          $('#kaizen_'+i).val(result.yokotenkai[i].kaizen).trigger('change');
          $('#tanggal_pengecekan_'+i).val(result.yokotenkai[i].tanggal_pengecekan);


          $('#pekerjaan_serupa_detail_'+i).val(result.yokotenkai[i].pekerjaan_serupa_detail);
          $('#peralatan_sejenis_detail_'+i).val(result.yokotenkai[i].peralatan_sejenis_detail);
          $('#kaizen_detail_'+i).val(result.yokotenkai[i].kaizen_detail);

          if (result.yokotenkai[i].pekerjaan_serupa_foto != null) {
            var url = "{{ url('files/kecelakaan/yokotenkai/') }}"+'/'+result.yokotenkai[i].pekerjaan_serupa_foto;
            $('#pekerjaan_serupa_foto_'+i).html("<br><img src='"+url+"' width='150px'>");
          }

          if (result.yokotenkai[i].peralatan_sejenis_foto != null) {
            var url = "{{ url('files/kecelakaan/yokotenkai/') }}"+'/'+result.yokotenkai[i].peralatan_sejenis_foto;
            $('#peralatan_sejenis_foto_'+i).html("<br><img src='"+url+"' width='150px'>");
          }

          if (result.yokotenkai[i].kaizen_sebelum != null) {
            var url = "{{ url('files/kecelakaan/yokotenkai/') }}"+'/'+result.yokotenkai[i].kaizen_sebelum;
            $('#kaizen_sebelum_'+i).html("<br><img src='"+url+"' width='150px'>");
          }

          if (result.yokotenkai[i].kaizen_sesudah != null) {
            var url = "{{ url('files/kecelakaan/yokotenkai/') }}"+'/'+result.yokotenkai[i].kaizen_sesudah;
            $('#kaizen_sesudah_'+i).html("<br><img src='"+url+"' width='150px'>");
          }

        }
      }else{
        openErrorGritter('Error!',result.message);
        audio_error.play();
      }
    });
  }

  $('#modalDetail').on('shown.bs.modal', function () {
    $('#tag').focus();
  }) 

  function foc(){
    $('#tag').focus();
  }

  $('#tag').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if(this.value.length > 7){
        scanTag(this.value);
      }
      else{
        $('#tag').val("");
        $('#tag').focus();
        openErrorGritter('Error!', 'ID Card invalid');
      }
    }
  });

  function scanTag(id){

    var accident_id = $('#accident_id').val();

    var data = {
      tag:id,
      accident_id:accident_id
    }

    $.post('{{ url("scan/yokotenkai/attendance") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tag').val("");
        $('#tag').focus();
        sosialisasi(accident_id);
        openSuccessGritter('Success!', result.message);
      }
      else{
        $('#tag').val("");
        $('#tag').focus();
        openErrorGritter('Error!', result.message);
      }
    });
  }


  function buttonImage(elem) {
    $(elem).closest("div").find("input").click();
  }

  function readURL(input,idfile) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          var img = $(input).closest("div").find("img#blah");
          $(img).show();
          $(img)
          .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }

      $(input).closest("div").find("button").hide();
    }

    function sosialisasi(id){
      var data = {
        id:id
      }

      $.get('{{ url("fetch/yokotenkai/attendance") }}', data, function(result, status, xhr) {
          $('#modalDetail').modal('show');
          foc();
          $('#loading').show();
          if(result.status){
            $('#loading').hide();
            $('#judul').text(result.accident.condition);
            $('#accident_id').val(result.accident.id);
            
            foc();

            var tableData = "";
            var count = 1;
            var count_all = 0;
            $('#tableDetailBody').html("");

            $.each(result.accident_details, function(key, value) {
              tableData += "<tr id='"+value.id+"''>";
              tableData += "<td>"+count+"</td>";
              tableData += "<td>"+value.employee_id+"</td>";
              tableData += "<td>"+value.name+"</td>";
              tableData += "<td>"+value.department+"</td>";
              tableData += "<td>"+value.attend_time+"</td>";
              tableData += "</tr>";
              count += 1;
              count_all += 1;
            });
            $('#count_all').text(count_all);
            $('#tableDetailBody').append(tableData);
          }
          else{
            audio_error.play();
            $('#loading').hide();
            $('#modalDetail').modal('hide');
            openErrorGritter('Error!', 'Attempt to retrieve data failed');
          }

        });
    }

    function ShowChart(id){
      $('#loading').show();

      var data = {
        id:id
      }

      $.get('{{ url("chart/yokotenkai") }}',data,function(result, status, xhr){
        if(result.status){

          $("#id_sosialiasi").val(id);
          $('#loading').hide();
          xCategories = [];
          belum = [];
          sudah = [];

          var total = 0;
          var total_belum = 0;
          var total_sudah = 0;

          mcu_detail = [];
          periode = '';

          for(var i = 0; i < result.department.length;i++){

            var count_sudah = 0;
            var count_belum = 0;
            var sosil = [];
            for(var j = 0; j < result.sosialisasi.length; j++){
              sosil.push(result.sosialisasi[j].employee_id);
            }

              for(var k = 0; k < result.employees.length;k++){
                if (sosil.includes(result.employees[k].employee_id) && result.employees[k].department == result.department[i].department_name) {
                    count_sudah++;
                    total_sudah++;
                    mcu_detail.push({
                      employee_id:result.employees[k].employee_id,
                      name:result.employees[k].name,
                      department_shortname:result.department[i].department_shortname,
                      department:result.department[i].department,
                      section:result.employees[k].section,
                      group:result.employees[k].group,
                      sub_group:result.employees[k].sub_group,
                      status_cek:'Sudah',
                    });
                }else if(!sosil.includes(result.employees[k].employee_id) && result.employees[k].department == result.department[i].department_name){
                  count_belum++;
                  total_belum++;
                  mcu_detail.push({
                    employee_id:result.employees[k].employee_id,
                    name:result.employees[k].name,
                    department_shortname:result.department[i].department_shortname,
                    department:result.department[i].department,
                    section:result.employees[k].section,
                    group:result.employees[k].group,
                    sub_group:result.employees[k].sub_group,
                    status_cek:'Belum',
                  });
                }
              }
            sudah.push({y:parseInt(count_sudah),key:result.department[i].department_name});
            belum.push({y:parseInt(count_belum),key:result.department[i].department_name});
            xCategories.push(result.department[i].department_shortname);
          }

          const chart = new Highcharts.Chart({
              chart: {
                  renderTo: 'container_sosialisasi',
                  type: 'column',
                  options3d: {
                      enabled: true,
                      alpha: 0,
                      beta: 0,
                      depth: 50,
                      viewDistance: 25
                  }
              },
              xAxis: {
              categories: xCategories,
              type: 'category',
              gridLineWidth: 0,
              gridLineColor: 'RGB(204,255,255)',
              lineWidth:1,
              lineColor:'#9e9e9e',
              labels: {
                style: {
                  fontSize: '13px'
                }
              },
            },
            yAxis: [{
              title: {
                text: 'Total Data',
                style: {
                  color: '#eee',
                  fontSize: '15px',
                  fontWeight: 'bold',
                  fill: '#6d869f'
                }
              },
              labels:{
                style:{
                  fontSize:"15px"
                }
              },
              type: 'linear',
              opposite: true
            }
            ],
            tooltip: {
              headerFormat: '<span>{series.name}</span><br/>',
              pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
            },
            legend: {
              layout: 'horizontal',
              itemStyle: {
                fontSize:'12px',
              },
              reversed : true
            },  
              title: {
                  text: ''
              },
              subtitle: {
                  text: ''
              },
              plotOptions: {
                  series:{
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,this.options.key);
                    }
                  }
                },
                animation: false,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style:{
                    fontSize: '1vw'
                  }
                },
                animation: false,
                cursor: 'pointer',
                depth:25
              },
              },
              credits:{
                enabled:false
              },
              series: [{
              type: 'column',
              data: belum,
              name: 'Belum Memahami',
              colorByPoint: false,
              color:'#f44336'
            },{
              type: 'column',
              data: sudah,
              name: 'Sudah Memahami',
              colorByPoint: false,
              color:'#32a852'
            }
            ]
          });
        }


        $('#judul_chart').html('Detail Sosialiasi Kecelakaan Kerja '+result.accident.condition);
        $('#modalChart').modal('show');
      })
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
