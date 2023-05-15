@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
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
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  #table_disribusi > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    background-color: #a488aa;
    padding: 2px;
  }

  #table_disribusi > tbody > tr > td{
    padding: 1vw 1vw 0 1vw;
    vertical-align: top;
    text-align: left;
    border: 1px solid black;
  }

  #table_document > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    background-color: #a488aa;
    padding: 2px;
  }

  #table_document > tbody > tr > td{
    vertical-align: middle;
    text-align: left;
    border: 1px solid black;
  }

  h3 {
    margin-top: 10px;
    margin-bottom: 5px;
  }

  .btn-upload {
    display: none;
  }
  #loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }}</span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if (session('success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('success') }}
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
      <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="sk_number">Nomor Sakurentsu <span class="text-purple">作連通の番号</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="sk_number" readonly="" value="{{ $judul->sakurentsu_number }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="sk_number" readonly="" value="">
                <?php } ?>
              </div>
            </div>

            <div class="col-xs-5">
              <div class="form-group">
                <label for="title">Judul Sakurentsu <span class="text-purple">作連通の表題</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="title" readonly="" value="{{ $judul->title }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="title" readonly="" value="">
                <?php } ?>
              </div>
            </div>

            <div class="col-xs-3">
              <div class="form-group">
                <label for="target">Tanggal Target <span class="text-purple">締切</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="target" readonly="" value="{{ $judul->tgl_target }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="target" readonly="" value="">
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-solid">
        <div class="box-body">
          <form enctype="multipart/form-data" id="main_form">
            <div class="row">
              <div class="col-xs-12">
                <center>
                  <h4>Form Aplikasi Perubahan 3M <span class="text-purple">( 3Ｍ変更 申請書 )</span></h4>
                  <h4>Form Informasi Perubahan 3M <span class="text-purple">( 3Ｍ変更 連絡通報 )</span></h4>
                </center>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-7">
                <div class="form-group">
                  <label for="date"><span class="text-red">*</span>Judul <span class="text-purple">件名</span></label>
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <input type="hidden" name="stat" value="3">
                  <input type="text" class="form-control input-lg" id="title_name" placeholder="Title" name="title_name">
                  <input type="text" class="form-control input-lg" id="title_name_trans" placeholder="Translate Title" name="title_name_trans">
                  <input type="hidden" class="form-control" id="id" name="id">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date"><span class="text-red">*</span>Nama Produk / Nama Mesin <span class="text-purple">製品名 / 設備名</span></label>
                  <input type="text" class="form-control" id="product_name" placeholder="Input Product Name / Machine Name" name="product_name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date"><span class="text-red">*</span>Nama Proses <span class="text-purple">工程名</span></label>
                  <input type="text" class="form-control" id="proccess_name" placeholder="Input Process Name" name="proccess_name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date"><span class="text-red">*</span>Nama Unit <span class="text-purple">班　名</span></label>
                  <input type="text" class="form-control" id="unit_name" placeholder="Input Unit Name" name="unit_name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <p><b><span class="text-red">*</span>Klasifikasi Perubahan 3M <span class="text-purple">3M変更区分</span></b></p>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Metode">Metode <span class="text-purple">工法</span>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Material">Material <span class="text-purple">材料</span>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Mesin">Mesin <span class="text-purple">設備</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="related_department"><span class="text-red">*</span>Departemen Terkait</label>
                  <select class="form-control select2" id="related_department" name="related_department[]" data-placeholder="Select Related Department" multiple="">
                    <option value=""></option>
                    @foreach($departemen as $dpr)
                    <option value="{{ $dpr->department }}">{{ $dpr->department }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label><span class="text-red">*</span>Isi dan Alasan Perubahan <span class="text-purple">変更内容・変更理由</span></label>
                  <textarea id="isi" name="isi"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label><span class="text-red">*</span>Keuntungan Perubahan <span class="text-purple">変更することによるメリット</span></label>
                  <textarea id="keuntungan" name="keuntungan"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label><span class="text-red">*</span>Pengecekan kualitas sebelumnya (Tgl・metode・jumlah・pengecek,dll) <span class="text-purple">事前の品質確認　（日時・方法・数量・確認者等）</span></label>
                  <textarea id="kualitas_before" name="kualitas_before"></textarea>
                </div>
              </div>
            </div>            

            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label><span class="text-red">*</span>Tanggal mulai・Tgl rencana perubahan <span class="text-purple">開始日・切替予定日</span> <br> ※alasan bila menjadi after request <span class="text-purple">※事後申請となった場合はその理由</span></label>
                  <div class="input-group date">
                    <div class="input-group-addon bg-purple" style="border: none;">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker" name="tgl_rencana" id="tgl_rencana" placeholder="Input Planned Start Date">
                  </div>
                  <label>Catatan Tanggal mulai・Tgl rencana perubahan </label>
                  <textarea id="tgl_rencana_note" name="tgl_rencana_note"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Item khusus <span class="text-purple">特記事項</span></label>
                  <textarea id="item_khusus" name="item_khusus"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <p><b><span class="text-red">*</span>Perubahan Bom <span class="text-purple">BOM変更</span></b></p>
                  <label class="radio-inline">
                    <input type="radio" name="bom_change" value="Ada">Ada 有り
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="bom_change" value="Tidak Ada">Tidak Ada 無し
                  </label>
                </div>
              </div>
            </div>

            <div class="row" style="display: none">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Lampiran <span class="text-purple">添付</span></label>
                  <table id="table_lampiran">
                  </table>
                  <input name="file[]" type="file" id="lampiran" multiple >
                  
                </div>
              </div>
            </div>

            <!--  ////////////////////////////       TABLE  DOCUMENT      /////////////////////////////////// -->
            <div class="row">
              <div class="col-xs-12">
                <table style="width: 100%" id="table_document">
                  <tr>
                    <th colspan="10" style="font-size: 16px; padding-top: 3px; padding-bottom: 3px;">
                      関　連　資　料　が　あ　る　場　合　は　添　付　す　る　事　　（試作作業連絡通報・作業連絡通報・その他関連資料）<br>
                      Lampirkan bila ada dokumen terkait (Form informasi trial・Form informasi kerja・dokumen terkait lainnya)
                    </th>
                  </tr>
                  <tr>
                    <th colspan="3">チェックリスト(発議部門記入) <br> Ceklist (Input dept. inisiatif)</th>
                    <th colspan="2">チェック <br> Cek</th>
                    <th>備考（特記事項　等）<br> Note (Note khusus,dll)</th>
                    <th style="width: 10%">完了期日 <br> Target Selesai</th>
                    <th style="width: 10%">完了日 <br> Tanggal Selesai</th>
                    <th style="width: 12%">確認者 <br> PIC</th>
                    <th style="width: 5%">#</th>
                  </tr>
                  <tr>
                    <td style="width: 4%; text-align: center">1</td>
                    <td style="padding-left: 2px; width: 15%">品質確認</td>
                    <td style="padding-left: 2px; width: 15%" id="head_1">Pengecekan Kualitas</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight: bold;"><label class="radio-inline"><input type="radio" name="doc_1" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight: bold;"><label class="radio-inline"><input type="radio" name="doc_1" class="doc"  value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_1" value="Pengecekan Kualitas">
                      <input type="text" class="form-control" placeholder="note..."  name="doc_note_1">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_1"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_1"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_1" id='doc_pic_1' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_1"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">2</td>
                    <td style="padding-left: 2px; width: 15%">コスト確認</td>
                    <td style="padding-left: 2px; width: 15%" id="head_2">Pengecekan Cost</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_2" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_2" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_2" value="Pengecekan Cost">
                      <input type="text" class="form-control" placeholder="note..." name="doc_note_2">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_2"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_2"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_2" id='doc_pic_2' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_2"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">3</td>
                    <td style="padding-left: 2px; width: 15%">関連工程への影響調査</td>
                    <td style="padding-left: 2px; width: 15%" id="head_3">Investigasi Efek ke Proses Terkait</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_3" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_3" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_3" value="Investigasi Efek ke Proses Terkait">
                      <input type="text" class="form-control" placeholder="note..." name="doc_note_3">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_3"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_3"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_3" id='doc_pic_3' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_3"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>



                  <tr>
                    <th colspan="3">他部門依頼文書 <br> Dokumen Request Dept. Lain</th>
                    <th colspan="2">新設or改訂 <br> New or Revise</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                  <tr>
                    <td style="text-align: center;">4</td>
                    <td style="padding-left: 2px; width: 15%">製造仕様書の変更</td>
                    <td style="padding-left: 2px; width: 15%" id="head_4">Perubahan Spec Produksi</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_4" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_4" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_4" value="Perubahan Spec Produksi">
                      <input type="text" class="form-control" placeholder="note ..." name="doc_note_4">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_4"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_4"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_4" id='doc_pic_4' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_4"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">5</td>
                    <td style="padding-left: 2px; width: 15%">加工図</td>
                    <td style="padding-left: 2px; width: 15%" id="head_5">Drawing Proses</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_5" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_5" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_5" value="Drawing Proses">
                      <input type="text" class="form-control" placeholder="note ..." name="doc_note_5">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_5"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_5"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_5" id='doc_pic_5' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_5"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">6</td>
                    <td style="padding-left: 2px; width: 15%">工程図</td>
                    <td style="padding-left: 2px; width: 15%" id="head_6">Flow Proses</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_6" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_6" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_6" value="Flow Proses">
                      <input type="text" class="form-control" placeholder="note ..." name="doc_note_6">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_6"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_6"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_6" id='doc_pic_6' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_6"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">7</td>
                    <td style="padding-left: 2px; width: 15%">検査仕様書</td>
                    <td style="padding-left: 2px; width: 15%" id="head_7">Form Spec Kensa</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name ="doc_7" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_7" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_7" value="Form Spec Kensa">
                      <input type="text" class="form-control" placeholder="note..." name="doc_note_7">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_7"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_7"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_7" id='doc_pic_7' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_7"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>
                  <tr>
                    <td style="text-align: center;">8</td>
                    <td style="padding-left: 2px; width: 15%">設備/治工具図</td>
                    <td style="padding-left: 2px; width: 15%" id="head_8">Drawing Tool Jig/Mesin</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_8" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_8" class="doc" value="NO">不要 NO</label></td>
                    <td>
                      <input type="hidden" name="doc_name_8" value="Drawing Tool Jig/Mesin">
                      <input type="text" class="form-control" placeholder="note ..." name="doc_note_8">
                    </td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_8"></td>
                    <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_8"></td>
                    <td>
                      <select class='form-control select3' name="doc_pic_8" id='doc_pic_8' data-placeholder="pic *" style="width:100%">
                        <option value=""></option>
                        @foreach($pics as $pic)
                        <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_8"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                  </tr>



                  <tr>
                    <th colspan="3">自部門管理文書 <br> Dokumen Kontrol Dept Internal</th>
                    <th colspan="2">新設or改訂 <br> New or Revise</th>
                    <th>文書Ｎｏ.  <br> Nomor Dokumen</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                  <tr>
                    <td style="text-align: center;">9</td>
                    <td style="padding-left: 2px; width: 15%">ＱＣ工程表（製造工程管理表）</td>
                    <td style="padding-left: 2px; width: 15%" id="head_9">QC Kouteihyou (Production Process Control List)</td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name ="doc_9" class="doc" value="NEED">要 NEED</label></td>
                    <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio"
                     name="doc_9" class="doc" value="NO">不要 NO</label>
                   </td>
                   <td>
                    <input type="hidden" name="doc_name_9" value="QC Kouteihyou (Production Process Control List)">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_9">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_9"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_9"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_9" id='doc_pic_9' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_9"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">10</td>
                  <td style="padding-left: 2px; width: 15%">作業基準書</td>
                  <td style="padding-left: 2px; width: 15%" id="head_10">IK Proses</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio"  name="doc_10" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_10" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_10" value="IK Proses">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_10">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_10"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_10"></td>
                  <td>
                    <select class='form-control select3' id="doc_pic_10" name="doc_pic_10" data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_10"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">11</td>
                  <td style="padding-left: 2px; width: 15%">検査基準書</td>
                  <td style="padding-left: 2px; width: 15%" id="head_11">IK Kensa</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_11" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_11" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_11" value="IK Kensa">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_11">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_11"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_11"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_11" id='doc_pic_11' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_11"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">12</td>
                  <td style="padding-left: 2px; width: 15%">設備点検基準書</td>
                  <td style="padding-left: 2px; width: 15%" id="head_12">IK Pengecekan Mesin</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_12" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_12" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_12" value="IK Pengecekan Mesin">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_12">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_12"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_12"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_12" id='doc_pic_12' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_12"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">13</td>
                  <td style="padding-left: 2px; width: 15%">治工具点検基準書</td>
                  <td style="padding-left: 2px; width: 15%" id="head_13">IK pengecekan Tool Jig</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_13" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_13" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_13" value="IK pengecekan Tool Jig">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_13">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_13"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_13"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_13" id='doc_pic_13' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_13"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>


                <tr>
                  <th colspan="6">その他 <br> DLL</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
                <tr>
                  <td style="text-align: center;">14</td>
                  <td style="padding-left: 2px; width: 15%">協力工場品質取決</td>
                  <td style="padding-left: 2px; width: 15%" id="head_14">Kesepakatan Kualitas Vendor</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_14" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_14" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_14" value="Kesepakatan Kualitas Vendor">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_14">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_14"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_14"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_14" id='doc_pic_14' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_14"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">15</td>
                  <td style="padding-left: 2px; width: 15%">製品仕様確認（SPEC）</td>
                  <td style="padding-left: 2px; width: 15%" id="head_15">Pengecekan spec produk (SPEC)</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_15" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_15" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_15" value="Pengecekan spec produk (SPEC)">
                    <input type="text" class="form-control" placeholder= "note..." name="doc_note_15">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_15"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_15"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_15" id='doc_pic_15' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_15"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">16</td>
                  <td style="padding-left: 2px; width: 15%">薬品確認（SDS等）</td>
                  <td style="padding-left: 2px; width: 15%" id="head_16">Pengecekan chemical (SDS)</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_16" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_16" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_16" value="Pengecekan chemical (SDS)">
                    <input type="text" class="form-control" placeholder= "note..." name="doc_note_16">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_16"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_16"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_16" id='doc_pic_16' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_16"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">17</td>
                  <td style="padding-left: 2px; width: 15%">リスクアセスメント</td>
                  <td style="padding-left: 2px; width: 15%" id="head_17">Risk assesment</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_17" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_17" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_17" value="Risk assesment">
                    <input type="text" class="form-control" placeholder="note..." name="doc_note_17">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_17"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_17"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_17" id='doc_pic_17' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_17"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>
                <tr>
                  <td style="text-align: center;">18</td>
                  <td style="padding-left: 2px; width: 15%">環境影響（ISO14001）</td>
                  <td style="padding-left: 2px; width: 15%" id="head_18">Efek lingkungan (ISO 14001)</td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_18" class="doc" value="NEED">要 NEED</label></td>
                  <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><label class="radio-inline"><input type="radio" name="doc_18" class="doc" value="NO">不要 NO</label></td>
                  <td>
                    <input type="hidden" name="doc_name_18" value="Efek lingkungan (ISO 14001)">
                    <input type="text" class="form-control" placeholder ="note..." name="doc_note_18">
                  </td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Target *" name="doc_target_18"></td>
                  <td><input type="text" class="form-control datepicker" placeholder="Tanggal Selesai" name="doc_finish_18"></td>
                  <td>
                    <select class='form-control select3' name="doc_pic_18" id='doc_pic_18' data-placeholder="pic *" style="width:100%">
                      <option value=""></option>
                      @foreach($pics as $pic)
                      <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_18"><i class="fa fa-folder-open"></i>&nbsp;File</button></center></td>
                </tr>

             <!--  <tr>
                <td><span style="-moz-transform: rotate(270deg); -webkit-transform: rotate(270deg); display: inline-block;">備考 Note</span></td>
                <td colspan="5"></td>
                <td>
                  ※事後申請案件で内容に疑義がある場合、決裁者からの対応指示事項（⇒後日、発議責任者は対応結果を同欄に記入）<br>
                  Pada subjek after request, bila ada hal yang kurang jelas pada isi, lihat poin instruksi penanganan oleh approver (⇒selanjutnya, pihak yang bertanggung jawab akan mencantumkan hasil penanganannya di kolom yang sama)removeAttr( "title" )
                </td>
                <td></td>
                <td></td>
                <td></td>
              </tr> -->
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12" style="background-color: #7cfa78; margin-top: 5px; margin-left: 5px; display: none" id="notif_saved">
            <center>Data Saved Successfuly</center>
          </div>
          <div class="col-xs-12">
            <button type="submit" class="btn btn-success pull-right" style="margin-top: 5px" id="save_3m"><i class="fa fa-check"></i>&nbsp; SAVE 3M</button>
            <button type="button" class="btn btn-primary pull-right" style="margin-top: 5px; margin-right: 5px" id="email_doc" onclick="modal_email()" disabled><i class="fa fa-envelope"></i>&nbsp; Email PIC Dokumen</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="modalFile">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="doc_desc"></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <table class="table table-hover table-bordered table-striped" id="tableFile">
            <tbody id='bodyFile'></tbody>
          </table>
          <iframe id="my_iframe" name="my_iframe" height="0" width="0" frameborder="0" scrolling="yes"></iframe>

          <form method="post" enctype="multipart/form-data" action="{{ url("upload/sakurentsu/3m/document") }}" id="form_upload">
            <label>Upload file(s)</label>
            <input type="file" name="doc_upload" id="doc_upload">
            <input type="hidden" name="text_doc_upload" id="text_doc_upload">
            <input type="hidden" name="id_doc_upload" id="id_doc_upload">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <br>
            <center><i class="fa fa-spin fa-refresh" style="display: none" id="loading-upload"></i></center>
            <button class="btn btn-success btn-sm" style="width: 100%" type="button" onclick="do_upload(this)" id="btn-upload-file"><i class="fa fa-plus"></i>&nbsp; Upload</button>
          </form>
          <button class="btn btn-default btn-sm" style="width: 100%" type="button" data-dismiss="modal"><i class="fa fa-close"></i>&nbsp; Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_email">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Kirim Notifikasi Email Kebutuhan Dokumen</h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <table class="table table-hover table-bordered table-striped" id="tableDetail">
            <thead style="background-color: #a488aa">
              <tr>
                <th>Document Name</th>
                <th>Note</th>
                <th>Target Date</th>
                <th>PIC</th>
                <th>Tanggal Selesai</th>
              </tr>
            </thead>
            <tbody id="bodyDetail">
            </tbody>
          </table>

          <button class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i>&nbsp; Cancel</button>
          <button class="btn btn-primary pull-right" onclick="sendMail()"><i class="fa fa-send"></i>&nbsp; Kirim Email</button>
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
<script src="{{ url("js/dropzone.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var file = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('input[type="radio"]').prop('checked', false);

    fillData();

    $(".select2").select2();
    $(".select3").select2({
      allowClear: true
    });

    $(".datepicker").datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    CKEDITOR.replace('isi' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: 300
    });

    CKEDITOR.replace('keuntungan' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: 150
    });

    CKEDITOR.replace('kualitas_before' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: 100
    });

    CKEDITOR.replace('tgl_rencana_note' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: 100
    });

    CKEDITOR.replace('item_khusus' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: 150
    });

  });

  function fillData() {
    var datas = <?php echo json_encode($data); ?>;

    $("#title_name").val(datas.title);
    $("#title_name_trans").val(datas.title_jp);
    $("#id").val(datas.id);
    $("#product_name").val(datas.product_name);
    $("#proccess_name").val(datas.proccess_name);
    $("#unit_name").val(datas.unit);
    $("input[name='category'][value='"+datas.category+"']").prop('checked', true);

    if (datas.related_department) {
      $.each(datas.related_department.split(","), function(index,value){
        $("#related_department option[value='" + value + "']").prop("selected", true);
      });
    }

    $("#isi").val(datas.reason);
    $("#keuntungan").val(datas.benefit);
    $("#kualitas_before").val(datas.check_before);
    $("#tgl_rencana").val(datas.started_date);
    $("#tgl_rencana_note").val(datas.date_note);
    $("#item_khusus").val(datas.special_items);
    $("input[name='bom_change'][value='"+datas.bom_change+"']").prop('checked', true);

    if (datas.att !== null) {
      tb_lamp = "";
      $("#table_lampiran").empty();

      $.each(datas.att.split(":"), function(index,value){
        tb_lamp += "<tr>";
        tb_lamp += "<td><a href='"+"{{ url('/uploads/sakurentsu/three_m/att/') }}/"+value+"' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp;"+value+"</a><td>";
        tb_lamp += "</tr>";
      });

      $("#table_lampiran").append(tb_lamp);
    }
  }

  $('#main_form').on('submit', function (e) {
    if (confirm('Anda Yakin Akan Menyimpan Perubahan 3M dan Telah Melakukan Meeting?')) {
      $("#loading").show();
      if($('input[name="bom_change"]:checked').val() == undefined){
        $("#loading").hide();
        openErrorGritter('Error', 'Harap Menentukan Perubahan BOM');
        return false;
      }

      for (var z = 1; z <= 18; z++) {
        if($('input[name="doc_'+z+'"]:checked').val() == undefined){
          $("#loading").hide();
          openErrorGritter('Error', 'Harap Mengisi Semua Pengecekan Dokumen Terkait');
          return false;
        }

        if($('input[name="doc_'+z+'"]:checked').val() == "NEED"){
          if($('input[name="doc_target_'+z+'"]').val() == '' || $('#doc_pic_'+z).val() == ''){
            $("#loading").hide();
            openErrorGritter('Error', 'Harap Mengisi Semua Keterangan Dokumen yang Dibutuhkan');
            return false;
          }
        }
      }

      e.preventDefault();

      var formData = new FormData();
      formData.append('id', $("#id").val());
      formData.append('product', $("#product_name").val());
      formData.append('proccess', $("#proccess_name").val());
      formData.append('title', $("#title_name").val());
      formData.append('title_jp', $("#title_name_trans").val());
      formData.append('unit_name', $("#unit_name").val());
      formData.append('category', $("input[name='category']:checked").val());
      formData.append('content', CKEDITOR.instances.isi.getData());
      formData.append('benefit', CKEDITOR.instances.keuntungan.getData());
      formData.append('kualitas_before', CKEDITOR.instances.kualitas_before.getData());
      formData.append('planned_date', $("#tgl_rencana").val());
      formData.append('planned_date_note', CKEDITOR.instances.tgl_rencana_note.getData());
      formData.append('special_item', CKEDITOR.instances.item_khusus.getData());
      formData.append('sakurentsu_number', $("#sk_number").val());
      formData.append('related_department', $("#related_department").val());
      formData.append('bom_change', $("input[name='bom_change']:checked").val());
      formData.append('stat', 3);

      for (var i = 1; i <= 18; i++) {
        formData.append('doc_'+i, $("input[name='doc_"+i+"']:checked").val());
        formData.append('doc_name_'+i, $("input[name='doc_name_"+i+"']").val());
        formData.append('doc_note_'+i, $("input[name='doc_note_"+i+"']").val());
        formData.append('doc_target_'+i, $("input[name='doc_target_"+i+"']").val());
        formData.append('doc_finish_'+i, $("input[name='doc_finish_"+i+"']").val());
        formData.append('doc_pic_'+i, $("#doc_pic_"+i).val());
      }


      $.each($('input[name="file[]"]'),function(i, obj) {
        $.each(obj.files,function(j,file){
          formData.append('file['+i+']['+j+']', file);
        })
      });

      var url = "{{ url('post/sakurentsu/3m/premeeting')}}";
      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function (response) {
          $("#loading").hide();
          $("#notif_saved").show();
          openSuccessGritter('Success', response.message);
          $("#email_doc").attr('disabled', false);
        },
        error: function (response) {
          $("#loading").hide();
          openErrorGritter('Error', response.message);
        },
        contentType: false,
        processData: false
      });
    } else {
      // window.stop();
      return false;
    }
  })

  $(".btn-upload").click(function() {
    var ido = $(this).attr('id').split('_')[1];

    var text = $("#head_"+ido).text();

    var data = {
      id : "{{ Request::segment(5) }}",
      doc_desc : text
    }

    $("#doc_upload").val('');
    $("#doc_desc").text(text);
    $("#text_doc_upload").val(text);
    $("#id_doc_upload").val("{{ Request::segment(5) }}");

    $.get('{{ url("fetch/sakurentsu/3m/document") }}', data, function(result, status, xhr){
      $("#bodyFile").empty();
      if (result.status) {
        body_file = "";
        $.each(result.docs, function(key, value) {  
          if (result.docs.length > 0) {
            body_file += "<tr>";
            body_file += "<td>";
            body_file += "<a href='"+"{{ url('uploads/sakurentsu/three_m/doc/') }}/"+value.file_name+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.file_name+"</a>";
            body_file += "</td>";
            body_file += "</tr>";
          }
        });

        $("#bodyFile").append(body_file);

        $("#modalFile").modal('show');
      } else {
        $("#modalFile").modal('show');
      }
    })
  });

  $('.doc').change(function(){
    if ($(this).val() == "NEED") {
      $("#"+$(this).attr('name')).css('display', 'block');
    } else {
      $("#"+$(this).attr('name')).css('display', 'none');
    }
  });

  function getFileInfo(num, sk_num) {
    $("#sk_num").text(sk_num+" File(s)");

    $("#bodyFile").empty();

    body_file = "";
    $.each(file, function(key, value) {  
      if (sk_num == value.sk_number) {
        var obj = JSON.parse(value.file);
        var app = "";

        if (obj) {
          for (var i = 0; i < obj.length; i++) {
           body_file += "<tr>";
           body_file += "<td>";
           body_file += "<a href='../../uploads/sakurentsu/translated/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
           body_file += "</td>";
           body_file += "</tr>";
         }
       }
     }
   });

    $("#bodyFile").append(body_file);

    $("#modalFile").modal('show');
  }

  function do_upload(elem)
  {
    $(elem).attr('disabled','disabled');
    $("#loading-upload").show();
    document.getElementById('form_upload').target = 'my_iframe';
    document.getElementById('form_upload').submit();
  }

// function save_3m() {
//   var data = {
//       // form_number : $("#number").val(),
//       // date: $("#date").val(),
//       product : $("#product_name").val(),
//       proccess : $("#proccess_name").val(),
//       title : $("#title").val(),
//       unit_name : $("#unit_name").val(),
//       category : $("input[name='category']:checked").val(),
//       content : CKEDITOR.instances.isi.getData(),
//       benefit : CKEDITOR.instances.keuntungan.getData(),
//       kualitas_before : CKEDITOR.instances.kualitas_before.getData(),
//       planned_date : $("#tgl_rencana").val(),
//       special_item : CKEDITOR.instances.item_khusus.getData(),
//       sakurentsu_number : $("#sk_number").val()
//       // bom_change : $("input[name='bom_change']:checked").val()
//     }

//     $.post('{{ url("post/sakurentsu/3m_form") }}', data, function(result, status, xhr){
//       if (result.status) {
//        openSuccessGritter('Success', '3M has been created');

//            // window.setTimeout( window.location.replace('{{ url("index/sakurentsu/list_3m") }}'), 3000);
//          } else {
//           openErrorGritter('Error', result.message);
//         }
//       })
//   }

function getdata(doc_name) {
 var data = {
  id : "{{ Request::segment(5) }}",
  doc_desc : doc_name
}

$("#btn-upload-file").removeAttr("disabled");
$("#loading-upload").hide();

$.get('{{ url("fetch/sakurentsu/3m/document") }}', data, function(result, status, xhr){
  $("#bodyFile").empty();
  if (result.status) {

    body_file = "";
    $.each(result.docs, function(key, value) {  
       //  if (sk_num == value.sk_number) {
       //    var obj = JSON.parse(value.file);
       //    var app = "";

       //    if (obj) {
       //      for (var i = 0; i < obj.length; i++) {
       //       body_file += "<tr>";
       //       body_file += "<td>";
       //       body_file += "<a href='../../uploads/sakurentsu/translated/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
       //       body_file += "</td>";
       //       body_file += "</tr>";
       //     }
       //   }
       // }

       body_file += "<tr>";
       body_file += "<td>";
       body_file += "<a href='"+"{{ url('uploads/sakurentsu/three_m/doc/') }}/"+value.file_name+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.document_name+"</a>";
       body_file += "</td>";
       body_file += "</tr>";
     });

    $("#bodyFile").append(body_file);
  }
})
}

function modal_email() {
  $("#modal_email").modal('show');

  $("#bodyDetail").empty();
  body = "";

  $('.doc').each(function(index, value) {
    var ido = $(this).attr('name');
    var num = ido.split('_')[1];

    if ($(this).is(':checked') && $('input[name="'+ido+'"]:checked').val() == "NEED") {    

      body += "<tr>";

      body += "<td>"+$("input[name='doc_name_"+num+"']").val()+"</td>";
      body += "<td>"+$("input[name='doc_note_"+num+"']").val()+"</td>";
      body += "<td>"+$("input[name='doc_target_"+num+"']").val()+"</td>";
      body += "<td>"+$("#doc_pic_"+num+" option:selected").val()+"</td>";
      body += "<td>"+$("input[name='doc_finish_"+num+"']").val()+"</td>";
      body += "</tr>";
    }
  });

  $("#bodyDetail").append(body);
}

function sendMail() {
  $("#loading").show();
  var data = {
    three_m_id : $("#id").val()
  }

  $.post('{{ url("mail/sakurentsu/3m/document") }}', data, function(result, status, xhr){
    $("#loading").hide();

    if (result.status) {
      $("#modal_email").modal('hide');
      openSuccessGritter('Success', 'Document Reminder has been Informed to PIC Document');
      $("#email_doc").attr('disabled', 'false');
    } else {
      openErrorGritter('Error', result.message);
    }
  })
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openErrorGritter(title, message) {
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-danger',
    image: '{{ url("images/image-stop.png") }}',
    sticky: false,
    time: '2000'
  });

  audio_error.play();
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '2000'
  });

  audio_ok.play();
}

</script>


<script>
  var msg = '{{Session::get('alert')}}';
  var exist = '{{Session::has('alert')}}';
  if(exist){
   window.parent.getdata("{{Session::get('doc_name')}}");
 }
</script>
@stop