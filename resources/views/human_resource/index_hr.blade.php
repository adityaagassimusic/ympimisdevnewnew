@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered {
    border: 1px solid black;
  }

  table.table-bordered>thead>tr>th {
    border: 1px solid black;
    vertical-align: middle;
    text-align: center;
  }

  table.table-bordered>tbody>tr>td {
    border: 1px solid rgb(100, 100, 100);
    padding: 3px;
    vertical-align: middle;
    height: 45px;
    text-align: center;
  }

  table.table-bordered>tfoot>tr>th {
    border: 1px solid rgb(100, 100, 100);
    vertical-align: middle;
  }

  .dataTables_info,
  .dataTables_length {
    color: white;
  }

  div.dataTables_filter label,
  div.dataTables_wrapper div.dataTables_info {
    color: white;
  }

  .nav-tabs-custom>ul.nav.nav-tabs {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  .nav-tabs-custom>ul.nav.nav-tabs>li {
    float: none;
    display: table-cell;
  }

  .nav-tabs-custom>ul.nav.nav-tabs>li>a {
    text-align: center;
  }

  #loading,
  #error {
    display: none;
  }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <small class="text-purple">{{ $title_jp }}</small>
  </h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
    @if (session('status'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
      {{ session('status') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-ban"></i> Error!</h4>
      {{ session('error') }}
    </div>   
    @endif
  </div>

  <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
    <div class="box-body">
      <div class="col-xs-6" style="margin-top: 10px; padding-left: 0px">
        <button id="click_simpati" onclick="InputMp()" class="btn btn-danger" style="font-weight: bold; font-size: 15px; width: 100%; color: white;">Permohonan Uang Simpati<br>お見舞いのお金</button>
      </div>
      <div class="col-xs-6" style="margin-top: 10px;  padding-right: 0px">
        <button id="click_tk" onclick="InputMp2()" class="btn btn-warning" style="font-weight: bold; font-size: 15px; width: 100%; color: white;">Permohonan Tunjangan Keluarga<br>家族手当の申請</button>
      </div>
    </div>
  </div>

  <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
    <div class="box-body">
      <div class="col-xs-12" style="background-color:  #E6DBD0 ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
       <span style="font-size: 25px;color: black;width: 25%;">Progres Approval Uang Simpati dan Tunjangan Keluarga</span>
     </div>
     <div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;vertical-align: middle; padding-top: 5px">
      <table id="tableResumeTunjangan" class="table table-bordered table-hover" style="margin-bottom: 0;">
        <thead style="background-color: rgb(126,86,134); color: #FFD700;">
          <tr>
            <th width="1%">Request ID</th>
            <th width="2%">Jenis Tunjangan</th>
            <th width="2%">Pemohon</th>
            <th width="2%">Leader / Sub Leader</th>
            <th width="2%">Chief / Foreman</th>
            <th width="2%">Manager</th>
            <th width="2%">Manager HR</th>
            <th width="1%">#</th>
            <th width="1%">#</th>
          </tr>
        </thead>
        <tbody style="background-color: #fcf8e3;" id="tableBodyResumeTunjangan">
        </tbody>
        <tfoot>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
  <div class="box-body">
    <div class="col-xs-10" style="background-color:  #E6DBD0 ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
      <span style="font-size: 25px;color: black;width: 25%;"><span id="view_bulan"></span></span>
    </div>
    <div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
      <input type="text" id="bulan_monitoring" class="form-control datepicker" style="width: 100%; height: 100%; text-align: center;" placeholder="Pilih Bulan" value="{{ date('Y-m') }}" onChange="FilterBulan(this.value)">
    </div>
    <div class="col-xs-12" style="padding-left: 0px;padding-right: 5px;vertical-align: middle; padding-top: 5px">
      <table id="tableFetchTunjangan" class="table table-bordered table-hover" style="width: 100%">
        <thead style="background-color: #605ca8; color: white;">
          <tr>
            <th style="width: 1%">No</th>
            <th style="width: 2%">Request Id</th>
            <th style="width: 3%">Nama Karyawan</th>
            <th style="width: 3%">Leader / Sub Leader</th>
            <th style="width: 3%">Jenis Pengajuan</th>
            <th style="width: 3%">Status</th>
          </tr>
        </thead>
        <tbody style="background-color: #fcf8e3;" id="bodyFetchTunjangan">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- modal -->
<div class="modal fade" id="modal_tp" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #bb8fce;">
          <h1 style="text-align: center; margin:5px; font-weight: bold;">Resume Pengajuan Tunjangan Pekerjaan</h1>
        </div>
        <div class="col-md-4" style="padding-top: 10px">
          <div class="form-group">
            <select class="form-control select2" id="filter_tp" name="filter_tp" data-placeholder="Select Filter" style="width: 100%;" onchange="ResumeTp(this.value)">
              <option value="">&nbsp;</option>
              <option value="sudah">Sudah Di Download</option>
              <option value="belum">Belum Di Download</option>
            </select>
          </div>
        </div>
        <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; overflow-x: scroll;">
          <center><h4 id="title_proses" style="font-weight: bold"></h4></center>
          <table class="table table-hover table-striped table-bordered" id="tableResumePekerjaan">
            <thead style="background-color: rgb(126,86,134); color: white;">
              <tr>
                <th >No</th>
                <th>Department</th>
                <th>Bulan</th>
                <th>Pembuat</th>
                <th>#</th>
              </tr>
            </thead>
            <tbody id="tableBodyResumePekerjaan">
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>    
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_simpati" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #bb8fce">
          <h1 style="text-align: center; margin:5px; font-weight: bold; width: 100%">Resume Pengajuan Uang Simpati</h1>
        </div>
        <div class="col-md-4" style="padding-top: 10px">
          <div class="form-group">
            <label>Tanggal Pengajuan Uang Simpati</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="date" name="date" onchange="ResumeUs()">
            </div>
          </div>
        </div>
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <form method="GET" action="{{ url("human_resource/download/simpati") }}">
          <div class="col-md-8" style="padding-top:35px">
            <div class="form-group">
              <button id="download" type="submit" class="btn btn-success" style="font-weight: bold; font-size: 15px;">Download <span style="font-size:10px">家族手当の申請</span></button>
            </div>
          </div>
        </form>
        <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%; width: 100%; overflow-x: scroll;">
          <center><h4 id="title_proses" style="font-weight: bold"></h4></center>
          <table class="table table-hover table-striped table-bordered" id="tableResumeTunjangan1">
            <thead style="background-color: rgb(126,86,134); color: white;">
              <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Department</th>
                <th>Jabatan</th>
                <th>Keterangan</th>
                <th>Proses</th>
                <th>#</th>
              </tr>
            </thead>
            <tbody id="tableBodyResumeTunjangan1">
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
        </div>    
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <form id ="AddUangSimpati" name="AddUangSimpati" method="post" action="{{ url('human_resource/add/uang_simpati') }}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
            <div class="box-body">
              <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                <div class="col-xs-12" style="background-color: #bb8fce;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
                  <span style="font-size: 25px;color: black;width: 25%;">Pengajuan Uang Simpati</span>
                  <span style="font-size: 25px;color: black;width: 25%;">お見舞いのお金</span>
                </div>
                <br><br>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>NIK<span class="text-red">*</span></label>
                    <select id="employee_id_us" name="employee_id_us" class="form-control select2" onchange="checkEmpUs(this.value)" data-placeholder="Pilih Karyawan" style="width: 100%; font-size: 20px;">
                      <option></option>
                      @foreach($user as $p)
                      <option value="{{ $p->employee_id }}">{{ $p->employee_id }} - {{ $p->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group" id="pemohonan">
                    <label id="label_section">Sub Group<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="sub_group_us" name="sub_group_us" readonly>
                  </div>
                  <div class="form-group">
                    <label id="label_group">Group<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="group_us" name="group_us" readonly>
                  </div>
                  <div class="form-group">
                    <label id="label_section">Seksi<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="section_us" name="section_us" readonly>
                  </div>
                  <div class="form-group">
                    <label id="labeldept">Department<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="department_us" name="department_us" readonly>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Jabatan<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="position_us" name="position_us" readonly>
                  </div>
                </div>
                <div class="col-md-6" id="detail_simpati">
                  <div class="form-group"><br>
                    <span>Dengan ini mengajukan permohonan untuk mendapatkan Uang Simpati bentuk : </span><br><br>
                    <div class="col-md-6">
                      <input type="radio" name="permohonan_us" value="Uang Simpati Pernikahan" onchange="checkPermohonan(this.value)"> Uang Simpati Pernikahan<br>  
                      <input type="radio" name="permohonan_us" value="Uang Simpati Kelahiran" onchange="checkPermohonan(this.value)"> Uang Simpati Kelahiran<br>  
                    </div>
                    <div class="col-md-6">
                      <input type="radio" name="permohonan_us" value="Uang Simpati Kematian" onchange="checkPermohonan(this.value)"> Uang Simpati Kematian<br>  
                      <input type="radio" name="permohonan_us" value="Uang Simpati Musibah" onchange="checkPermohonan(this.value)"> Uang Simpati Musibah<br>
                    </div>
                  </div>
                  <br><br><br><br>
                  <div class="form-group">
                    <div class="col-md-12" id="srt_nikah">
                      <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                      <span style="font-weight: bold; font-size: 16px;">Surat Nikah<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_nikah_us" name="surat_nikah_us">
                    </div>
                    <div class="col-md-12" id="srt_lahir">
                      <select class="form-control select2" data-placeholder="Pilih" id="simp_anak" name="simp_anak" style="width: 100%">
                        <option style="color:grey;" value="">Pilih</option>
                        <option value="Anak Kembar">Anak Kembar</option>
                        <option value="Anak Ke 1">Anak Ke 1</option>
                        <option value=" Anak Ke 2">Anak Ke 2</option>
                        <option value="Anak Ke 3">Anak Ke 3</option>
                      </select> 
                      <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                      <span style="font-weight: bold; font-size: 16px;">Akte Kelahiran / Surat Kenal Lahir<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_akte_us" name="surat_akte_us">
                    </div>
                    <div class="col-md-12" id="srt_kematian">
                      <select class="form-control select2" data-placeholder="Pilih" id="simp_kematian" name="simp_kematian" style="width: 100%">
                        <option style="color:grey;" value="">Pilih</option>
                        <option value="Isteri | Suami">Kematian Isteri/Suami</option>
                        <option value="Anak">Kematian Anak</option>
                        <option value="Orang Tua | Mertua">Kematian Orang Tua/Mertua</option>
                        <option value="Pekerja Sendiri">Pekerja Sendiri</option>
                      </select>
                      <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                      <span style="font-weight: bold; font-size: 16px;">Surat Kematian<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_kematian_us" name="surat_kematian_us">
                      <span style="font-weight: bold; font-size: 16px;">Kartu Keluarga<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_kematian_kk" name="surat_kematian_kk">
                    </div>
                    <div class="col-md-12" id="srt_lain">
                      <select class="form-control select2" data-placeholder="Pilih" id="simp_musibah" name="simp_musibah" style="width: 100%">
                        <option style="color:grey;" value="">Pilih</option>
                        <option value="Kebanjiran">Kebanjiran</option>
                        <option value="Kebakaran Rumah">Kebakaran Rumah</option>
                      </select> 
                      <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                      <span style="font-weight: bold; font-size: 16px;">Surat Keterangan Musibah<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_lain_us" name="surat_lain_us">
                    </div>
                    <div class="col-md-12" style="padding-top: 30px">
                      <span style="font-weight: bold; font-size: 16px; color: red;">File Upload Harus Format JPG<span class="text-red">*</span></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12" style="margin-bottom : 5px">
                  <button id="kirim_us" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 100%;" type="submit">Kirim Pengajuan<br>提出物を提出する</button>
                </div>
              </div>
            </div>
          </div>
        </form>   
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCreate2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <form id ="AddUangKeluarga" name="AddUangKeluarga" method="post" action="{{ url('human_resource/add/uang_keluarga') }}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
            <div class="box-body">
              <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                <div class="col-xs-12" style="background-color: #bb8fce;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
                  <span style="font-size: 25px;color: black;width: 25%;">Tunjangan Keluarga</span>
                  <span style="font-size: 25px;color: black;width: 25%;">家族手当</span>
                </div>
                <br><br>
                <div class="col-md-6">          
                  <div class="form-group">
                    <label>NIK<span class="text-red">*</span></label>
                    <select id="employee_id_tk" name="employee_id_tk" class="form-control select3" onchange="checkEmpTk(this.value)" data-placeholder="Pilih Karyawan" style="width: 100%; font-size: 20px;">
                      <option></option>
                      @foreach($user_tk as $p)
                      <option value="{{ $p->employee_id }}">{{ $p->employee_id }} - {{ $p->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label id="label_section">Sub Group<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="sub_group_tk" name="sub_group_tk" readonly>
                  </div>
                  <div class="form-group">
                    <label id="label_group">Group<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="group_tk" name="group_tk" readonly>
                  </div>
                  <div class="form-group">
                    <label id="label_section">Seksi<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="section_tk" name="section_tk" readonly>
                  </div>
                  <div class="form-group">
                    <label id="labeldept">Department<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="department_tk" name="department_tk" readonly>
                  </div>
                  <div class="form-group">
                    <label id="labelposition">Jabatan<span class="text-red">*</span></label>
                    <input type="text" class="form-control" id="position_tk" name="position_tk" readonly>
                  </div>
                </div>
                <div class="col-md-6" id="detail_keluarga">
                  <div class="form-group"><br>
                    <span>Dengan ini mengajukan permohonan untuk mendapatkan Tunjangan Keluarga, yaitu : </span><br>
                  </div>
                  <div class="col-md-6">
                    <input type="radio" name="tunj_kel" value="Tunjangan Pasangan" onchange="checkPermohonanKeluarga(this.value)"> Tunjangan Pasangan<br>
                  </div>
                  <div class="col-md-6">
                    <input type="radio" name="tunj_kel" value="Tunjangan Anak" onchange="checkPermohonanKeluarga(this.value)"> Tunjangan Anak<br>
                  </div>
                  <br><br><br><br>
                  <div class="form-group" id="tunj_isteri">
                    <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                    <div class="col-md-12">
                      <span style="font-weight: bold; font-size: 16px;">Surat Nikah<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_nikah_tk" name="surat_nikah_tk">
                    </div><br>
                  </div>
                  <div class="form-group" id="tunj_anak">
                    <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
                    <div class="col-md-12">
                      <select class="form-control select3" data-placeholder="Pilih" id="anak_tk" name="anak_tk" style="width: 100%">
                        <option style="color:grey;" value="">Pilih</option>
                        <option value="Anak Kembar">Anak Kembar</option>
                        <option value="Anak Ke 1">Anak Ke 1</option>
                        <option value=" Anak Ke 2">Anak Ke 2</option>
                        <option value="Anak Ke 3">Anak Ke 3</option>
                      </select>
                      <span style="font-weight: bold; font-size: 16px;">Akte Kelahiran / Surat Kenal Lahir<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg" id="surat_akte_tk" name="surat_akte_tk">
                    </div><br>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12">
                      <span style="font-weight: bold; font-size: 16px;">Kartu Keluarga<span class="text-red">*</span></span>
                      <input type="file" class="form-control-file" accept="image/png, image/gif, image/jpeg"  id="surat_lain_tk" name="surat_lain_tk">
                    </div>
                    <div class="col-md-12" style="padding-top: 30px">
                      <span style="font-weight: bold; font-size: 16px; color: red;">File Upload Harus Format JPG<span class="text-red">*</span></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12" style="margin-bottom : 5px">
                  <button id="kirim_tk" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 100%;" type="submit">Kirim Pengajuan<br>提出物を提出する</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCreate3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <form id ="AddUangKeluarga" name="AddUangKeluarga" method="post" action="{{ url('human_resource/add/uang_keluarga') }}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
            <div class="box-body">
              <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                <div class="col-xs-12" style="background-color: #bb8fce;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
                  <span style="font-size: 25px;color: black;width: 25%;">Tunjangan Proses Kerja</span>
                  <span style="font-size: 25px;color: black;width: 25%;">家族手当</span>
                </div>
              </div>
            </div>
          </div>
        </form>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
<script>
  var no = 2;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var no = 1;
  var arr_employee_id_tp = [];
  var arr_in_out_tp = [];
  var arr_tanggal_tp = [];
  var arr_keterangan_tp = [];
  var arr_loop_tp = [];
  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


  jQuery(document).ready(function() {
   $('body').toggleClass("sidebar-collapse");
   Home();
   // fillChart();
   ResumeUs();
   $('.select2').select2({
    allowClear : true,
    dropdownParent:$('#modalCreate')
  });

   $('.select3').select2({
    allowClear : true,
    dropdownParent:$('#modalCreate2')
  });

   $('.select4').select2({
    allowClear : true,
    dropdownParent:$('#modalCreate2')
  });

   $('.select5').select2({
    allowClear : true
  });

   FilterBulan("{{ date('Y-m') }}");
 });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

  $('#tanggal').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
  });

  $('#date').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
  });
  $('#datem').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
  });

  $("form#AddUangSimpati").submit(function(e) {
    $("#loading").show();
    if( document.getElementById("asset_foto").files.length == 0 ){
      openErrorGritter('Error', 'No files selected');
      $("#loading").hide();
      return false;
    }
  });

  $("form#AddUangKeluarga").submit(function(e) {
    $("#loading").show();
    if( document.getElementById("asset_foto").files.length == 0 ){
      openErrorGritter('Error', 'No files selected');
      $("#loading").hide();
      return false;
    }
  });

  function FilterBulan(value){
    $("#loading").show();
    var q = new Date(value);
    var strArray=['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var arr_bulan = strArray[q.getMonth()];

    $('#view_bulan').html('Resume Uang Simpati dan Tunjangan Keluarga Bulan '+arr_bulan);

    var data = {
      bulan:value
    }
    $.get('<?php echo e(url("fetch/resume/tunjangan")); ?>', data, function(result, status, xhr){
      if (result.status) {
        $("#loading").hide();
        $('#tableFetchTunjangan').DataTable().clear();
        $('#tableFetchTunjangan').DataTable().destroy();
        $('#bodyFetchTunjangan').html("");
        var isiTabel = "";
        $.each(result.resumes, function(key, value) {
          var tunjangan = value.permohonan.split('/');
          var nik_leader = value.nik_leader.toUpperCase();
          isiTabel += '<tr>';
          isiTabel += '<td style="text-align: center;">'+ (key+1) +'</td>';
          isiTabel += '<td style="text-align: center;">'+ value.request_id +'</td>';
          isiTabel += '<td style="text-align: center;">('+ value.nik_karyawan +') - '+value.nama_karyawan+'</td>';
          isiTabel += '<td style="text-align: center;">('+ nik_leader +') - '+value.nama_leader+'</td>';
          isiTabel += '<td style="text-align: center;">'+ tunjangan[0] +'</td>';
          if (value.status == 'Open') {
            isiTabel += '<td style="text-align: center;"><span class="label label-info" style="color: white">Prosess Approval</span></td>';
          }else if (value.status == 'Close') {
            isiTabel += '<td style="text-align: center;"><span class="label label-warning" style="color: white">Approval Selesai, Menunggu Konfirmasi HR</span></td>';
          }else if (value.status == 'Done' || value.status == 'Sudah Download') {
            isiTabel += '<td style="text-align: center;"><span class="label label-success" style="color: white">Sudah Dikonfirmasi HR</span></td>';
          }else if (value.status == 'Rejected') {
            isiTabel += '<td style="text-align: center;"><span class="label label-danger" style="color: white">Permohonan Ditolak</span></td>';
          }else{
            isiTabel += '<td style="text-align: center;">'+value.status+'</td>';
          }
          isiTabel += '</tr>';
        });
        $('#bodyFetchTunjangan').append(isiTabel);
        var table = $('#tableFetchTunjangan').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'excel',
              className: 'btn btn-info',
              text: '<i class="fa fa-file-excel-o"></i> Excel',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            },
            ]
          },
          'paging': true,
          'lengthChange': true,
          'searching': true,
          'ordering': true,
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": false,
        });
      }else{
        $("#loading").hide();
        openErrorGritter('Error!',data.message);
      }
    });
  }

  function InputMp(){
    $('#modalCreate').modal('show');
    $("#srt_nikah").hide();
    $("#srt_lahir").hide();
    $("#srt_kematian").hide();
    $("#srt_lain").hide();
  }

  function InputMp2(){
    $('#modalCreate2').modal('show');
    $("#tunjangan_pekerjaan").hide();
    $("#uang_simpati").hide();
    $("#tunj_isteri").hide();
    $("#tunj_anak").hide();
  }

  function InputMp3(){
    $('#modalCreate3').modal('show');
  }

  function fillChart(value) {
    $("#loading").show();
    var dept = $('#select_dept').val();
    var data = {
      dept:dept,
      value:value
    }

    $.get('{{ url("fetch/grafik/tunjangan") }}', data,function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          $("#loading").hide();

          var dept = [];
          var hasil = [];
          var jumlah_kel = [];
          var jumlah_simp = [];

          $.each(result.tj_kel, function(key, value) {
            dept.push(value.department_shortname);
            jumlah_kel.push(value.jumlah);
          });

          $.each(result.tj_simp, function(key, value) {
            dept.push(value.department_shortname);
            jumlah_simp.push(6);
          });
          var colors = ['#6cbaff'];

          Highcharts.chart('container1', {
            chart: {
              type: 'column',
              options3d: {
                enabled: true,
                alpha: 15,
                beta: 7,
                depth: 50,
                viewDistance: 25
              }
            },
            title: {
              text: ''
            },
            xAxis: {
              categories: dept,
              type: 'category',
              gridLineWidth: 1,
              gridLineColor: 'RGB(204,255,255)',
              lineWidth:2,
              lineColor:'#9e9e9e',

              labels: {
                style: {
                  fontSize: '13px'
                }
              },
            },yAxis: [{
              title: {
                text: 'Total',
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
              opposite: true,
              tickInterval: 1
            },
            ],
            tooltip: {
              headerFormat: '<span>{series.name}</span><br/>',
              pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
            },
            legend: {
              layout: 'horizontal',
              backgroundColor:
              Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
              itemStyle: {
                fontSize:'10px',
              },
              enabled: true,
              reversed: true
            },  
            plotOptions: {
              series:{
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name);
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
                pointPadding: 0.93,
                groupPadding: 0.93,
                borderWidth: 0.93,
                cursor: 'pointer'
              },
            },credits: {
              enabled: false
            },
            colors:colors,
            series: [{
              data: hasil,
              name: 'Pending',
              showInLegend: false
            }
            ,{
              data: jumlah_simp,
              name: 'Uang Simpati',
              showInLegend: false
            }
            ]
          });
        }
      }
    });
  }

  function SimpatiPernikahan(){
    $("#srt_nikah").show();
    $("#srt_lahir").hide();
    $("#srt_kematian").hide();
    $("#srt_lain").hide();
  }

  function SimpatiKelahiran(){
    $("#srt_nikah").hide();
    $("#srt_lahir").show();
    $("#srt_kematian").hide();
    $("#srt_lain").hide();
  }

  function SimpatiKematian(){
    $("#srt_nikah").hide();
    $("#srt_lahir").hide();
    $("#srt_kematian").show();
    $("#srt_lain").hide();
  }

  function SimpatiMusibah(){
    $("#srt_nikah").hide();
    $("#srt_lahir").hide();
    $("#srt_kematian").hide();
    $("#srt_lain").show();
  }

  function TunjanganIsteri(){
    $("#tunj_isteri").show();
    $("#tunj_anak").hide();
  }

  function TunjanganAnak(){
    $("#tunj_isteri").hide();
    $("#tunj_anak").show();
  }

  function CekValueAnak(value){
    if (value > 3) {
      openErrorGritter('Error!', 'Maksimal Sampai Dengan Anak Ke - 3');
      $("#kirim_us").hide();
    }else if(value !== ""){
      openSuccessGritter('Success!');
    }
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


  function Home(){
    $("#tunjangan_pekerjaan").hide();
    $("#uang_simpati").hide();
    $("#tunjangan_keluarga").hide();
  }

  function DivTunjanganPekerjaan(){
    $("#tunjangan_pekerjaan").show();
    $("#uang_simpati").hide();
    $("#tunjangan_keluarga").hide();
  }

  function SelectSection(){
    var data = {
      department_tp:$('#department_tp').val()
    }

    $.get('{{ url("human_resource/get_section") }}',data, function(result, status, xhr){
      if(result.status){
        $('#section_tp').show();
        $('#section_tp').html("");
        var sections = "";
        sections += '<option value="">&nbsp;</option>';
        $.each(result.section, function(key, value) {
          sections += '<option value="'+value.section+'">'+value.section+'</option>';
        });

        $('#section_tp').append(sections);
      }
    });
  }

  function ResumeTp(filter){
    var filter = filter;

    var data = {
      filter_tp:filter
    }
    $.get('<?php echo e(url("human_resource/resume_uang_pekerjaan")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResumePekerjaan').DataTable().clear();
        $('#tableResumePekerjaan').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResumePekerjaan').html("");
        $('#tableBodyResumePekerjaan').empty();
        $("#modal_tp").modal('show');

        var urlreport = '{{ url("human_resource/detail_pekerjaan") }}';

        var count = 1;

        $.each(result.resumes, function(key, value) {
          tableData += '<tr>';
          tableData += '<td>'+ count +'</td>';
          tableData += '<td>'+ value.department +'</td>';
          tableData += '<td>'+ value.bulan +'</td>';
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td><a class="btn btn-success btn-xs" href="'+urlreport+'/'+value.department+'/'+value.bulan+'" style="color:white">Detail</a></td>';
          tableData += '</tr>';
          count += 1;
        });

        $('#tableResumePekerjaan tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
        } );

        $('#tableBodyResumePekerjaan').append(tableData);
        var tableResumePekerjaan = $('#tableResumePekerjaan').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [10, 25, 50, -1], [10, 25, 50, "All"]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            }
            ]
          },
          'paging': true,
          'lengthChange': false,
          'pageLength': 10,
          'searching': true,
          'ordering': true,
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        tableResumePekerjaan.columns().every( function () {
          var that = this;
          $( '#search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          } );
        } );

        $('#tableResumePekerjaan tfoot tr').appendTo('#tableResumePekerjaan thead');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function DivUangSimpati(value){
    $("#uang_simpati").show();
    $("#tunjangan_pekerjaan").hide();
    $("#tunjangan_keluarga").hide();
    $("#srt_nikah").hide();
    $("#srt_lahir").hide();
    $("#srt_kematian").hide();
    $("#srt_lain").hide();
    ResumeUs(value);
  }

  function checkEmpUs(value) {
    var data = {
      employee_id_us:$('#employee_id_us').val()
    }

    $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
      if(result.status){

        $('#employee_id_us').show();
        $('#sub_group_us').show();
        $('#group_us').show();
        $('#section_us').show();
        $('#department_us').show();
        $('#position_us').show();

        $.each(result.employee, function(key, value) {
          $('#employee_id_us').val(value.employee_id);
          $('#sub_group_us').val(value.sub_group);
          $('#group_us').val(value.group);
          $('#section_us').val(value.section);
          $('#department_us').val(value.department);
          $('#position_us').val(value.position);
          $('#detail_simpati').show();
          $('#kirim_us').show();
        });
      }
      else{
        openErrorGritter('Error!', result.message);
        $('#sub_group_us').val('');
        $('#group_us').val('');
        $('#section_us').val('');
        $('#department_us').val('');
        $('#position_us').val('');
        $('#detail_simpati').hide();
        $('#kirim_us').hide();
      }
    });         
  }

  function checkPermohonan(value){
    var employee_id_us = $('#employee_id_us').val();
    var data = {
      employee_id_us:employee_id_us,
      jenis:value
    }

    $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
        if (value == 'Uang Simpati Pernikahan') {
          SimpatiPernikahan();
        }else if(value == 'Uang Simpati Kelahiran'){
          SimpatiKelahiran();
        }else if(value == 'Uang Simpati Kematian'){
          SimpatiKematian();
        }else if (value == 'Uang Simpati Musibah') {
          SimpatiMusibah();
        }
      }
      else{
        openErrorGritter('Error!', result.message);
        hideDiv();
      }
    });  
  }

  function hideDiv(){
    $("#srt_nikah").hide();
    $("#srt_lahir").hide();
    $("#srt_kematian").hide();
    $("#srt_lain").hide();
  }

  function ResumeUs(value){
    var pic = $('#pic_progress').val();
    var data = {
      value:value,
      pic:pic
    }

    $.get('<?php echo e(url("human_resource/resume_uang_simpati")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResumeTunjangan').DataTable().clear();
        $('#tableResumeTunjangan').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResumeTunjangan').html("");
        $('#tableBodyResumeTunjangan').empty();

        var urlreport = '{{ url("human_resource/detail_simpati/") }}';

        var count = 1;

        $.each(result.resumes, function(key, value) {

          var appr = value.approver.split(",");
          var nik = value.approver_nik.split(",");
          var stt = value.status.split(",");
          var time = value.approved_at.split(",");
          tableData += '<tr>';
          tableData += '<td style="font-weight:bold;font-size:11x;">'+ value.request_id +'</td>';
          tableData += '<td style="font-weight:bold;font-size:11x;">'+ value.project_name +'</td>';
          if (stt[0] == null) {
            tableData += '<td style="background-color:red;color:white;font-weight:bold;font-size:11x;cursor:pointer;">'+appr[0]+'<br>Waiting</td>';
          }else if(stt[0] == 'none'){
            tableData += '<td style="background-color:black"></td>';
          }else{
            tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+appr[0]+'<br>'+time[0]+'<br>'+stt[0]+'</td>';
          }

          if (stt[0] == 'Pemohon') {
            if (stt[1] == 'none' || stt[1] == null) {
              tableData += '<td style="background-color:black"></td>';
            }else{
              tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[1]+'">'+appr[1]+'<br>'+time[1]+'<br>'+stt[1]+'</a></td>';
            }  
          }else{
            tableData += '<td style="background-color:black"></td>';
          }
          
          if (stt[1] == 'Mengetahui' || stt[1] == 'none' || stt[1] == 'Menyetujui' || stt[1] == 'Waiting') {
            if (stt[2] == 'none' || stt[2] == null) {
              tableData += '<td style="background-color:black"></td>';
            }else if (stt[2] == 'Waiting') {
              tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[2]+'">'+appr[2]+'<br>Waiting</a></td>';
            }else{
              tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[2]+'">'+appr[2]+'<br>'+time[2]+'<br>'+stt[2]+'</a></td>';
            }
          }else{
            tableData += '<td style="background-color:black"></td>';
          }

          if (stt[2] == 'Mengetahui' || stt[2] == 'none' || stt[2] == 'Pembuat' || stt[2] == 'Waiting') {
           if (stt[3] == 'none' || stt[3] == null) {
            tableData += '<td style="background-color:black"></td>';
          }else if (stt[3] == null || stt[3] == '' || stt[3] == 'Waiting') {
            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[3]+'">'+appr[3]+'<br>Waiting</a></td>';
          }else{
            if (stt[3] == 'Rejected') {
              tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11x;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank"</a>'+appr[3]+'<br>'+stt[3]+'<br>'+time[3]+'</td>';
            }else{
              tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[3]+'">'+appr[3]+'<br>'+time[3]+'<br>'+stt[3]+'</a></td>';
            }
          }
        }else{
          tableData += '<td style="background-color:black"></td>';
        }
        
        if (stt[3] == 'Mengetahui' || stt[3] == 'none' || stt[3] == 'Waiting') {
          if (stt[4] == null || stt[4] == '' || stt[4] == 'Waiting') {
            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" href="{{ url('tunjangan/approval/sign/') }}/'+value.request_id+'/'+nik[4]+'">'+appr[4]+'<br>Waiting</a></td>';
          }else{
            tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11x;cursor:pointer;">'+appr[4]+'<br>'+stt[4]+'<br>'+time[4]+'</td>';
          }  
        }else{
          tableData += '<td style="background-color:black"></td>';
        }

        tableData += '<td style=" text-align: center;">';
        tableData += '<a onclick="resendMail(\''+value.request_id+'\', \''+value.project_name+'\')" class="btn btn-info btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-send"></i> Resend<br>Email</a><br>';
        tableData += '</td>';
        tableData += '<td style=" text-align: center;">';
        tableData += '<a onclick="deleteTunjangan(\''+value.request_id+'\', \''+value.project_name+'\')" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="Send Mail"><i class="fa fa-trash-o"></i> Hapus</a><br>';
        tableData += '</td>';
        tableData += '</tr>';
        count += 1;
      });

$('#tableResumeTunjangan tfoot th').each( function () {
  var title = $(this).text();
  $(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
} );

$('#tableBodyResumeTunjangan').append(tableData);
var tableResumeTunjangan = $('#tableResumeTunjangan').DataTable({
  'dom': 'Bfrtip',
  'responsive':true,
  'lengthMenu': [
  [25, 50, -1], [25, 50, "All"]
  ],
  'buttons': {
    buttons:[
    {
      extend: 'pageLength',
      className: 'btn btn-default',
    }
    ]
  },
  'paging': true,
  'lengthChange': false,
  'pageLength': 10,
  'searching': true,
  'ordering': true,
  'info': true,
  'autoWidth': true,
  "sPaginationType": "full_numbers",
  "bJQueryUI": true,
  "bAutoWidth": false,
  "processing": true
});
tableResumeTunjangan.columns().every( function () {
  var that = this;
  $( '#search', this.footer() ).on( 'keyup change', function () {
    if ( that.search() !== this.value ) {
      that
      .search( this.value )
      .draw();
    }
  } );
} );

$('#tableResumeTunjangan tfoot tr').appendTo('#tableResumeTunjangan thead');
}
else{
  openErrorGritter('Error!', result.message);
}
});
}

function resendMail(request_id, project_name){
  var data = {
    project_name : project_name
  }

  if(confirm("Apakah anda yakin akan mengirim ulang email?")){
    $("#loading").show();
    $.get('{{ url("send/ulang/email/tunjangan") }}/'+request_id, data, function(result, status, xhr){
      openSuccessGritter('Success!', result.message);
      $("#loading").hide();
      audio_ok.play();

    });
  }
  else{
    return false;
  }
}

function deleteTunjangan(request_id, project_name){
  var data = {
    project_name : project_name
  }
  if(confirm("Apakah anda yakin akan menghapus permohonan ini?")){
    $("#loading").show();
    $.get('{{ url("delete/permohonan/tunjangan") }}/'+request_id, data, function(result, status, xhr){
      openSuccessGritter('Success!', result.message);
      $("#loading").hide();
      audio_ok.play();
      Home();
      ResumeUs();
    });
  }
  else{
    return false;
  }
}

function DownloadSimpati(){
  $.get('{{ url("human_resource/download/simpati") }}', function(result, status, xhr){
    if(result.status){
      $('#modal_simpati').show();
      openSuccessGritter('Success!', result.message);
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
}

function DivTunjanganKeluarga(value){
  $("#tunjangan_keluarga").show();
  $("#tunjangan_pekerjaan").hide();
  $("#uang_simpati").hide();
  $("#tunj_isteri").hide();
  $("#tunj_anak").hide();
  ResumeUs(value);
}

function ResumeUk(){
  var datem = $('#datem').val();

  var data = {
    datem:datem
  }

  $.get('<?php echo e(url("human_resource/resume_uang_keluarga")); ?>', data, function(result, status, xhr){
    if(result.status){
      $('#tableResumeKeluarga').DataTable().clear();
      $('#tableResumeKeluarga').DataTable().destroy();
      var tableData = '';
      $('#tableBodyResumeKeluarga').html("");
      $('#tableBodyResumeKeluarga').empty();
      $("#modal_uk").modal('show');

      var urlreport = '{{ url("human_resource/detail_keluarga/") }}';
      var count = 1;

      $.each(result.resumes, function(key, value) {
        tableData += '<tr>';
        tableData += '<td>'+ count +'</td>';
        tableData += '<td>'+ value.employee +'</td>';
        tableData += '<td>'+ value.name +'</td>';
        tableData += '<td>'+ value.department +'</td>';
        tableData += '<td>'+ value.jabatan +'</td>';
        tableData += '<td>'+ value.permohonan +'</td>';
        tableData += '<td>';
        if (value.remark == 'belum'){
          tableData += '<span class="label label-success label-xs">All Approved</span>';  
        }else{
          tableData += '<span class="label label-warning label-xs">Prosess</span>';  
        }
        tableData += '</td>';
        tableData += '<td><a class="label label-info label-xs" href="'+urlreport+'/'+value.id+'" style="color:white">Detail</a></td>';
        tableData += '</tr>';
        count += 1;
      });
      $('#tableResumeKeluarga tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );

      $('#tableBodyResumeKeluarga').append(tableData);
      var tableResumeKeluarga = $('#tableResumeKeluarga').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [10, 25, 50, -1], [10, 25, 50, "All"]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          }
          ]
        },
        'paging': true,
        'lengthChange': false,
        'pageLength': 10,
        'searching': true,
        'ordering': true,
          // 'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

      tableResumeKeluarga.columns().every( function () {
        var that = this;
        $( '#search', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#tableResumeKeluarga tfoot tr').appendTo('#tableResumeKeluarga thead');
    }
    else{
      openErrorGritter('Error!', result.message);
    }
  });
}

function checkEmpTk(value) {
  var data = {
    employee_id_tk:$('#employee_id_tk').val()
  }

  $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
    if(result.status){

      $('#employee_id_tk').show();
      $('#sub_group_tk').show();
      $('#group_tk').show();
      $('#section_tk').show();
      $('#department_tk').show();
      $('#position_tk').show();

      $.each(result.employee, function(key, value) {
        $('#employee_id_tk').val(value.employee_id);
        $('#sub_group_tk').val(value.sub_group);
        $('#group_tk').val(value.group);
        $('#section_tk').val(value.section);
        $('#department_tk').val(value.department);
        $('#position_tk').val(value.position);
        $('#detail_keluarga').show();
        $('#kirim_tk').show();
      });
    }else{
      openErrorGritter('Error!', result.message);
      $('#sub_group_tk').val('');
      $('#group_tk').val('');
      $('#section_tk').val('');
      $('#department_tk').val('');
      $('#position_tk').val('');
      $('#detail_keluarga').hide();
      $('#kirim_tk').hide();
    }
  });         
}

function checkPermohonanKeluarga(value){
  var employee_id_tk = $('#employee_id_tk').val();
  var data = {
    employee_id_tk:employee_id_tk,
    jenis:value
  }

  $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
    if(result.status){
      openSuccessGritter('Success!', result.message);
      if (value == 'Tunjangan Pasangan') {
        TunjanganIsteri();
      }else if(value == 'Tunjangan Anak'){
        TunjanganAnak();
      }
    }
    else{
      openErrorGritter('Error!', result.message);
      hideDivKeluarga();
    }
  });  
}

function hideDivKeluarga(){
  $("#tunj_isteri").hide();
  $("#tunj_anak").hide();
}

function add_item() {
  var bodi = "";
  var loop_tp = "";
  var employee_id_tp = "";
  var in_out_tp = "";
  var tanggal_tp = "";
  var keterangan_tp = "";

  employee_id_tp += "<option value=''></option>";
  in_out_tp += "<option value=''></option>";
  keterangan_tp += "<option value=''></option>";


  bodi += '<tr id="'+no+'" class="item">';

  bodi += '<td>';
  bodi += '<input type="text" name="loop_tp_'+no+'" id="loop_tp_'+no+'" value="coba'+no+'" hidden>';
  bodi += '<select class="form-control select2" id="employee_id_tp_'+no+'" name="employee_id_tp_'+no+'" data-placeholder="Pilih NIK Atau Nama" style="width: 100%"><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<select class="form-control select2" id="in_out_tp_'+no+'" name="employee_id_tk_'+no+'" data-placeholder="IN / OUT" style="width: 100%"><option value="">&nbsp;</option><option value="IN">IN</option><option value="OUT">OUT</option></select>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker" id="tanggal_'+no+'" name="tanggal_'+no+'" placeholder="Received Date"></div>';
  bodi += '</td>';

  bodi += '<td>';
  bodi += '<input type="text" class="form-control" id="keterangan_'+no+'" name="employee_id_tk_'+no+'">'
  bodi += '</td>';

  bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';

  bodi += '</tr>';

  $("#body_add").append(bodi);

  $.each(arr_loop_tp, function(index, value){
   loop_tp += "<option value='"+value+"'>"+value+"</option>";
 })

  $.each(arr_employee_id_tp, function(index, value){
   employee_id_tp += "<option value='"+value+"'>"+value+"</option>";
 })

  $.each(arr_in_out_tp, function(index, value){
   in_out_tp += "<option value='"+value+"'>"+value+"</option>";
 })

  $.each(arr_tanggal_tp, function(index, value){
   tanggal_tp += "<option value='"+value+"'>"+value+"</option>";
 })

  $.each(arr_keterangan_tp, function(index, value){
   keterangan_tp += "<option value='"+value+"'>"+value+"</option>";
 })

  no++;
  $('.select2').select2({
    allowClear : true
  });

  $(".datepicker").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true,
  });
}

function save_item() {
  arr_params = [];

  $('.item').each(function(index, value) {
   var ido = $(this).attr('id');
   arr_params.push({'employee_id_tp' : $("#employee_id_tp_"+ido).val(), 'in_out_tp' : $("#in_out_tp_"+ido).val(), 'tanggal_tp' : $("#tanggal_"+ido).val(), 'keterangan_tp' : $("#keterangan_"+ido).val(), 'loop_tp' : $("#loop_tp_"+ido).val()});
 });

  var data = {
   item : arr_params,
   department_tp : $('#department_tp').val(),
   section_tp : $('#section_tp').val().split('_')[0],
   bulan_tp : $('#bulan_tp').val()
 }

 $.post('{{ url("human_resource/add/uang_pekerjaan") }}', data, function(result, status, xhr) {
   location.reload(true);
 })
}

function delete_item(no) {
  $("#"+no).remove();
}


</script>

@endsection