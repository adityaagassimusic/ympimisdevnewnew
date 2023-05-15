@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">

  input {
    line-height: 22px;
  }
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
    color: black;
  }
  tfoot>tr>th{
    text-align:center;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  .content-wrapper{
    color: white;
    font-weight: bold;
    background-color: #313132 !important;
  }

  .input {
    text-align: center;
    font-weight: bold;
  }

  #loading, #error { display: none; }

  .loading {
    margin-top: 8%;
    position: absolute;
    left: 50%;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
  }
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
<section class="content" style="padding-top:0">
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <div class="row">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
      </p>
    </div>



    <div class="col-xs-8">
      <div class="col-xs-12" style="padding-left: 0; padding-bottom: 4px;">
        <h2 style="font-weight: bold;margin-top: 10px">&nbsp;&nbsp;&nbsp;CEK DIMENSI MATERIAL 寸法検査表 <span style="color: #ef6c00">( Z-PROJECT )</span><span style="color:#ff8f00;"> </span></h2>
      </div>
    </div>


    <!-- <div class="col-xs-4 col-xs-offset-4">
      <div class="col-xs-12" style="padding-left: 0; padding-bottom: 10px" align="center">
        <h2 style="font-weight: bold">Cek Dimensi Material <span class="text-purple">寸法検査表</span></h2>
        <h3 style="font-weight: bold; margin-top: 0px">Z - PROJECT</h3>
      </div>
    </div> -->

    <div class="col-xs-2" style="padding:0">
      <a class="btn btn-warning" href="{{ url('winds/index/description_item') }}/{{ $poin_cek[0]->gmc }}/{{ $id_process }}" aria-label="Close" style="font-weight: bold; font-size: 15px; width: 100%;"><i class="fa fa-chevron-left"></i> Back to Detail Item <br><i class="fa fa-chevron-left"></i> アイテム詳細に戻る</a>
    </div>
    <div class="col-xs-2">
      <a class="btn btn-success" href="{{ url('winds') }}" style="font-weight: bold; font-size: 15px; width: 100%;"><i class="fa fa-chevron-left"></i> Back to Dashboard <br><i class="fa fa-chevron-left"></i>  ダッシュボードに戻る</a>
    </div>


    <div class="col-xs-12">
      <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
        <div class="box-body">
          <table class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px;">
            <thead>
              <tr>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center">Dok. Level 書類レベル</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 30%; background-color: rgb(126,86,134); text-align: center">Part Name 部品名称</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center">No. Dokumen 書類番号</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center;">Revisi 改訂</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 20px;font-weight: bold">5</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 30px;font-weight: bold">{{ $poin_cek[0]->gmc }} - {{ $poin_cek[0]->deskripsi }}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 20px;font-weight: bold">YMPI/ZPJ/FM/001</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 20px;font-weight: bold">00</td>
              </tr>
            </tbody>
          </table>


        </div>
      </div>
    </div>

    <div class="col-xs-12">
      <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
        <div class="box-body">
          <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
            <div class="col-xs-12" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;" >
              <span style="font-size: 25px;color: black;width: 25%;">Poin Cek 確認項目</span>
              <span style="font-size: 25px;color: #ef6c00;float: right;">{{ $poin_cek[0]->gmc }} - {{$process}}</span>
            </div>
            <table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
              <tr>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: rgb(126,86,134); text-align: center; width: 1%">No</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: rgb(126,86,134); text-align: center">Poin Cek 確認項目</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: rgb(126,86,134); text-align: center">Standar 基準(mm)</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center">Minimum 最小値 (mm)</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center;">Maksimum 最大値 (mm)</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: rgb(126,86,134); text-align: center;">Metode 検査方法</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: rgb(126,86,134); text-align: center;">Frekuensi 頻度</th>
              </tr>
              <?php 
              $num = 1;
              ?>
              @foreach($poin_cek as $pc)
              <tr>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{$num}}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{$pc->poin_cek}}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{$pc->standar}}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">
                  {{$pc->min}}
                  <input type="hidden" class="form-control pull-right" id="standar_min_<?= $num ?>" name="standar_min_<?= $num ?>" value="{{$pc->min}}">
                </td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">
                  {{$pc->max}}
                  <input type="hidden" class="form-control pull-right" id="standar_max_<?= $num ?>" name="standar_max_<?= $num ?>" value="{{$pc->max}}">
                </td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{$pc->metode}}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{$pc->frekuensi}}</td>
              </tr>
              <?= $num++ ?>
              @endforeach
            </table>
            <br>
            <br>

            <form id="importForm" name="importForm" method="post" action="{{ url('winds/index/cdm/input') }}" enctype="multipart/form-data">

              <input type="hidden" value="{{csrf_token()}}" name="_token" />

              <div class="col-xs-12" style="padding: 0;vertical-align: middle;" >
                <table style="width: 100%" class="table table-bordered">
                  <tr>
                    <th colspan="3" style="background-color: #ef6c00; border: 1px solid black;">
                      <span style="font-size: 25px;color: white;width: 25%;">Input Hasil Pengecekan チェック結果入力 : </span>
                    </th>
                  </tr>
                  <tr>
                    <td style="background-color: #ffbd87; border: 1px solid black;">
                      <label style="color:black">Date 日付 : <span class="text-red">*</span></label>
                      <div class="input-group date">
                        <div class="input-group-addon"> 
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
                        <input type="hidden" value="{{ Request::segment(7) }}" name="process_num">
                        <input type="hidden" class="form-control pull-right" id="submission_date" name="submission_date" value="{{date('Y-m-d')}}" >
                        <input type="hidden" class="form-control pull-right" id="gmc" name="gmc" value="{{ $poin_cek[0]->gmc }}">
                        <input type="hidden" class="form-control pull-right" id="process" name="process" value="{{ Request::segment(6) }}">
                        <input type="hidden" class="form-control pull-right" id="id_process" name="id_process" value="{{ $id_process }}">
                        <input type="hidden" class="form-control pull-right" id="jumlah" name="jumlah" value="{{ $jumlah_poin_cek[0]->jumlah }}">
                      </div>
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;">
                      <label style="color:black">Operator / Inputor 作業者・入力者 : <span class="text-red">*</span></label>
                      <select class="form-control select2" id="inputor_id" name="inputor_id" style="width: 100%;" data-placeholder="Nama Operator" required="">
                        <option></option>
                        @foreach($operator as $op)
                        <option value="{{$op->employee_id}}_{{$op->name}}">{{$op->employee_id}} - {{$op->name}}</option>
                        @endforeach
                      </select>
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;">
                      <label style="color:black">Note 備考 :</label>

                      <input type="text" class="form-control pull-right" id="note" name="note" placeholder="Catatan">
                    </td>
                  </tr>
                </table>
              </div>
              <br><br>

              <table class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px;margin-top: 10px">
                <thead>
                  <tr>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: #ef6c00; text-align: center; width: 1%">No</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 20%; background-color: #ef6c00; text-align: center">Poin Cek 確認項目</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 30%; background-color: #ef6c00; text-align: center">Keterangan 基準</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: #ef6c00; text-align: center">Awal 初品</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: #ef6c00; text-align: center;">Tengah 中間品</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: #ef6c00; text-align: center;">Akhir 終品</th>
                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: #ef6c00; text-align: center;">Penilaian 評価</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 

                  $no = 1;

                  ?>
                  @foreach($poin_cek as $pc)
                  <tr>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      {{ $no }}
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      {{$pc->poin_cek}}
                      <input type="hidden" id="poin_cek_<?= $no ?>" name="poin_cek_<?= $no ?>" value="{{$pc->poin_cek}}">
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      @if($pc->remark == "Input hasil pengukuran")
                      Input hasil pengukuran 測定結果を記入
                      @elseif($pc->remark == "Keputusan OK/NG")
                      Keputusan OK/NG (OK・NG判断)
                      @endif
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      @if($pc->remark == "Input hasil pengukuran")
                      <input type="text" id="cdm_awal_<?= $no ?>" name="cdm_awal_<?= $no ?>" class="numpad form-control" placeholder="Input Awal" required="" onchange="checkinput(this)"> 
                      @elseif($pc->remark == "Keputusan OK/NG")
                      <select class="form-control select2" id="cdm_awal_<?= $no ?>" name="cdm_awal_<?= $no ?>" style="width: 100%;" data-placeholder="Kondisi Awal" required="" onChange="checkselect(this)">
                        <option></option>
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>
                      </select>
                      @endif
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      @if($pc->remark == "Input hasil pengukuran")
                      <input type="text" id="cdm_tengah_<?= $no ?>" name="cdm_tengah_<?= $no ?>" class="numpad form-control" placeholder="Input Tengah" required="" onchange="checkinput(this)"> 
                      @elseif($pc->remark == "Keputusan OK/NG")
                      <select class="form-control select2" id="cdm_tengah_<?= $no ?>" name="cdm_tengah_<?= $no ?>" style="width: 100%;" data-placeholder="Kondisi Tengah" required="" onChange="checkselect(this)">
                        <option></option>
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>
                      </select>
                      @endif
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                      @if($pc->remark == "Input hasil pengukuran")
                      <input type="text" id="cdm_akhir_<?= $no ?>" name="cdm_akhir_<?= $no ?>" class="numpad form-control" placeholder="Input Akhir" required="" onchange="checkinput(this)"> 
                      @elseif($pc->remark == "Keputusan OK/NG")
                      <select class="form-control select2" id="cdm_akhir_<?= $no ?>" name="cdm_akhir_<?= $no ?>" style="width: 100%;" data-placeholder="Kondisi Akhir" required="" onChange="checkselect(this)">
                        <option></option>
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>
                      </select>
                      @endif
                    </td>
                    <td style="background-color: #ffbd87; border: 1px solid black;vertical-align: middle;">
                     <input type="text" id="penilaian_<?= $no ?>" name="penilaian_<?= $no ?>" class="form-control" placeholder="Kesimpulan"> 
                   </td>
                 </tr>

                 <?php $no++ ?>

                 @endforeach
               </tbody>
             </table>
             <br>
             <div style="text-align:right;">
              <a href="{{url('winds/export/cdm')}}/{{ $poin_cek[0]->gmc }}/{{ Request::segment(6) }}" style="float: left;"><span class="fa fa-download" style="font-size:30px"></span> <b style="font-size:25px"><u>Export Data To Excel エクセルへのデータエクスポート</u></b>
              </a>
              <?php if (Auth::user()->role_code != 'WINDS') { ?>
                <button type="submit" name="submit" class="btn btn-success btn-lg" style="width:30%"><span class="fa fa-save" style="font-size:30px"> Save Data データ保存</span>
                <?php } ?>
              </div>
            </form>
            <br>

            <table class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px;">
              <thead>
                <tr>
                  <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 50%; background-color: rgb(126,86,134); text-align: center;">Drawing 図面</th>
                  <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 50%; background-color: rgb(126,86,134); text-align: center;">
                    <?php if ($poin_cek[0]->gmc == 'VFE087A'){ ?>
                      Metode
                    <?php } else { ?>
                      Nomor Offset オフセット番号
                    <?php } ?>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php 
                  $no_urut = '';

                  if (Request::segment(7) > 1) {
                    $no_urut = '2';
                  }
                  $proses = Request::segment(6);

                  if ($proses == 'Shinogi_MC') {
                    $proses = 'WC';
                  }

                  $proses .= $no_urut;
                  ?>
                  <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 20px;font-weight: bold"><img src="{{url('winds_file/offset')}}/drawing-{{ $poin_cek[0]->gmc }}-{{ $proses }}.png" style="max-width: 70%" alt="Drawing File"></td>
                  <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;font-size: 20px;font-weight: bold"><img src="{{url('winds_file/offset')}}/offset-{{ $poin_cek[0]->gmc }}-{{ $proses }}.png" style="max-width: 70%" alt="Offset File"></td>
                </tr>
              </tbody>
            </table>

            <hr style="border: 1px solid red;background-color: red">

            <div class="col-xs-12" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;" >
              <span style="font-size: 25px;color: black;width: 25%;">History Data Input CDM {{ $poin_cek[0]->gmc }} データ入力履歴 </span>
            </div>

            <table id="table" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
              <tr>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 15%; background-color: rgb(126,86,134); text-align: center;color: white;">Process</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 15%; background-color: rgb(126,86,134); text-align: center;color: white;">Date </th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 40%; background-color: rgb(126,86,134); text-align: center;color: white;">Inputor Name</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 40%; background-color: rgb(126,86,134); text-align: center;color: white;">Note</th>
                <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 10%; background-color: rgb(126,86,134); text-align: center;color: white;">Detail</th>
              </tr>
              @foreach($cdms_data as $cdm)
              <tr>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{ $cdm->proses }}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;"><?php echo date('d M Y', strtotime($cdm->tanggal)) ?></td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{ $cdm->inputor_id }} - {{ $cdm->inputor_name }}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;">{{ $cdm->note }}</td>
                <td style="background-color: #e8daef; border: 1px solid black;vertical-align: middle;"><button class="btn btn-warning" onclick="fetchDetail({{$cdm->id}})"><i class="fa fa-eye"></i></button></td>

              </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <center><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;color: black;"></h4></center>
        </div>
        <div class="modal-body">
          <table class="table table-bordered" id="tableDetail">
            <thead>
              <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:rgb(126,86,134);">
                <th style="width: 1%">No.</th>
                <th style="width: 5%">Poin Cek</th>
                <th style="width: 1%">Awal</th>
                <th style="width: 1%">Tengah</th>
                <th style="width: 1%">Akhir</th>
                <th style="width: 1%">Penilaian</th>
              </tr>
            </thead>
            <tbody id="bodyTableDetail">

            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
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
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%;"></table>';
  $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
  $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
  $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
  $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
  $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

  jQuery(document).ready(function() {
    $('.numpad').numpad({
      hidePlusMinusButton : true,
      decimalSeparator : '.'
    });

    if("{{session('status')}}" == "Data CDM Berhasil Dibuat"){
      openSuccessGritter('Berhasil','Data CDM Berhasil Dibuat');
    } else if("{{session('error')}}" == "Gagal") {
      openErrorGritter('Gagal','Data CDM Gagal Dibuat');
    }else{

    }

  });

  //input

  function checkinput(elem){
    var id = elem.id;
    var baris = id.split("_");
    var isi = elem.value;
    console.log(parseFloat($('#cdm_awal_'+baris[2]).val()));
    console.log(parseFloat($('#cdm_tengah_'+baris[2]).val()));
    console.log(parseFloat($('#cdm_akhir_'+baris[2]).val()));

  //   if ((parseFloat($('#cdm_awal_'+baris[2]).val()) >= parseFloat($('#standar_min_'+baris[2]).val()) && parseFloat($('#cdm_awal_'+baris[2]).val()) <= parseFloat($('#standar_max_'+baris[2]).val())) && 
  //     (parseFloat($('#cdm_tengah_'+baris[2]).val()) >= parseFloat($('#standar_min_'+baris[2]).val()) && parseFloat($('#cdm_tengah_'+baris[2]).val()) <= parseFloat($('#standar_max_'+baris[2]).val())) &&
  //     (parseFloat($('#cdm_akhir_'+baris[2]).val()) >= parseFloat($('#standar_min_'+baris[2]).val()) && parseFloat($('#cdm_akhir_'+baris[2]).val()) <= parseFloat($('#standar_max_'+baris[2]).val()))
  //     ) {
  //     isi = "OK";
  //   color = "green";
  // }else{
  //   isi = "NG";
  //   color = "red";
  // }

  // console.log(id);
  // console.log(isi);

  if (isi >= parseFloat($('#standar_min_'+baris[2]).val()) &&  isi <= parseFloat($('#standar_max_'+baris[2]).val()) ) {
    isi = "OK";
    color = "green";
  }else{
    isi = "NG";
    color = "red";
  }

      //2 iku opo

      $('#penilaian_'+baris[2]).val(isi).css({'background-color' : color , 'color' : 'white'}).attr('readonly','true');
    }

  //select

  function checkselect(elem){
    var id = elem.id;
    var baris = id.split("_");
    var isi = elem.value;

    if ( $('#cdm_awal_'+baris[2]).val() == "NG" || $('#cdm_tengah_'+baris[2]).val() == "NG" || $('#cdm_akhir_'+baris[2]).val() == "NG") {
      isi = "NG";
      color = "red";
    }else{
      isi = "OK";
      color = "green";
    }

    $('#penilaian_'+baris[2]).val(isi).css({'background-color' : color , 'color' : 'white'}).attr('readonly','true');

  }

  function fetchDetail(id) {

    $('#loading').show();
    var data = {
      id:id
    }

    $.get('{{ url("winds/index/cdm/detail") }}',data, function(result, status, xhr){
      if(result.status){
        $('#myModalLabel').html("CDM Detail <b>"+result.cdms[0].gmc+" - "+result.cdms[0].proses+"</b>");

        $('#tableDetail').DataTable().clear();
        $('#tableDetail').DataTable().destroy();
        $('#bodyTableDetail').html("");

        var total_point = 0;
        var tableData = "";

        $.each(result.cdms, function(key, value) {
          tableData += '<tr>';
          tableData += '<td style="width: 10%;border:1px solid black;padding:2px">'+ parseInt(key+1) +'</td>';
          tableData += '<td style="width: 50%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px">'+ value.poin_cek +'</td>';
          tableData += '<td style="width: 10%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px">'+ value.awal +'</td>';
          tableData += '<td style="width: 10%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px">'+ value.tengah +'</td>';
          tableData += '<td style="width: 10%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px">'+ value.akhir +'</td>';
          if (value.penilaian == "NG") {
            tableData += '<td style="width: 10%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px;background-color:red;color:white">'+ value.penilaian +'</td>';
          }else if (value.penilaian == "OK"){
            tableData += '<td style="width: 10%;text-align:center;vertical-align:middle;border:1px solid black;padding:2px;background-color:green;color:white">'+ value.penilaian +'</td>';
          }

          tableData += '</tr>';
        });

        $("#bodyTableDetail").append(tableData);

        var table = $('#tableDetail').DataTable({
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
          'pageLength': 10,
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

        $('#loading').hide();

        $('#modalDetail').modal('show');
      }else{
        $('#loading').hide();
        openErrorGritter('Error!','Failed Get Data');
      }
    });
    
  }

  $('.select2').select2({
    // minimumResultsForSearch : -1,
    allowClear: true,
    dropdownAutoWidth : true
  });

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