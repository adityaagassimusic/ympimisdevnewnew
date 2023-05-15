<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
  <title>YMPI 情報システム</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <style type="text/css">
    table tr td,
    table tr th{
      font-size: 7pt;
      border-collapse: collapse;
    }

    /*table {
      page-break-inside: auto;
    }*/

    /*.page-break {
        page-break-after: always;
    }*/
    body{
      -webkit-box-shadow:inset 0px 0px 0px 10px #f00;
    -moz-box-shadow:inset 0px 0px 0px 10px #f00;
    box-shadow:inset 0px 0px 0px 10px #f00;
    }

   @font-face {
        font-family: 'Arial';
        src: url("{{ storage_path('data_file\arial.ttf') }}");
        font-style: normal;
    }

    .ttd {
        font-family: arial;
      }

    @page { margin: 30px 30px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body style="">
  <div style="width: 100%;">
    <img width="100%" src="{{ url('/data_file/qa/atas.png') }}">
    <?php $colspan = count($data_approval_atas); ?>
    <table style="width: 100%;">
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 15px"><img width="125px" src="{{ url('/data_file/yamaha.png') }}"></th>
      </tr>
       <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 20px;font-weight: bold;font-size: 11pt">
          PT. YAMAHA MUSICAL PRODUCTS INDONESIA ( PT. YMPI )
          <hr style="border: 0.5pt solid #4f81bd;width: 90%;padding: 0px;margin-top: 0px;margin-bottom: 0px">
          <span style="font-weight: normal;font-size: 12pt;margin-top: 0px;padding-top: 0px">Commitment to Competence</span>
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 5px;font-weight: normal;font-size: 23pt">
          Certificate
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 12pt">
          To Whom It May Concern :
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 20pt">
          {{strtoupper($datas[0]->name)}}
          <hr style="border: 0.5pt solid #4f81bd;width: 70%;padding: 0px;margin-top: 0px;margin-bottom: 0px">
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 11pt;padding-top: 10px">
          Employee ID . {{strtoupper($datas[0]->employee_id)}}
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 11pt">
          PT. YMPI, {{$datas[0]->certificate_name}}
        </th>
      </tr>
      <tr>
        <!-- <th colspan="{{$colspan}}" style="text-align: center;padding-top: 100px; transform: rotate(270deg);"> -->
        <th colspan="{{$colspan}}" style="text-align: center;margin: -3% 0 0 0 !important;">
          <div style="overflow: hidden;">
            <img width="100px" style="margin: -2% 0 0 0 !important;" style="" src="{{ url('/images/avatar/'.$datas[0]->employee_id.'.jpg') }}">
          </div>
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 10pt;font-weight: normal;padding-top: 10px">
          Has the competence as an Inspector to check
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 11pt;font-weight: normal;">
          {{($datas[0]->certificate_desc)}}
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 8pt;font-weight: normal;padding-top: 6px;padding-bottom: 5pt;">
          Approved by,
        </th>
      </tr>
      <tr>
        @foreach($data_approval_atas as $data_approval_atas)
        <th style="font-size: 8pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;padding-top: 0px">
          <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;">{{$data_approval_atas->remark}}</div>
          <?php if ($data_approval_atas->approver_status == null){ ?>
            <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;padding: 15px"><br></div>
          <?php }else{ ?>
            <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;color: green;font-weight: bold;font-size: 7pt;">Approved<br>{{explode(' ', $data_approval_atas->approved_at)[0]}}<br>{{explode(' ', $data_approval_atas->approved_at)[1]}}</div>
          <?php } ?>
          <span style="text-align: center;padding-top: 0px;margin-top: 0px">{{$data_approval_atas->approver_name}}</span><br>
        </th>
        @endforeach
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="padding-top: 7px">
          <hr style="border: 0.1pt solid #4f81bd;width: 100%;padding: 0px;margin-top: 0px;margin-bottom: 0px">
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="font-size: 7pt;width: 1%;padding-top: 5px">
          <table>
            <tr>
              <td style="font-size: 7pt;width: 1%;">Issued Date </td>
              <td style="font-size: 7pt;width: 1%;">: {{date('d F Y',strtotime($datas[0]->periode_from))}}</td>
            </tr>
            <tr>
              <td style="font-size: 7pt;width: 1%;">Expired Date </td>
              <td style="font-size: 7pt;width: 1%;">: {{date('d F Y',strtotime($datas[0]->periode_to))}}</td>
            </tr>
            <tr>
              <td style="font-size: 7pt;width: 1%;">Certificate No. </td>
              <td style="font-size: 7pt;width: 1%;">: {{strtoupper($datas[0]->certificate_code)}}</td>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <th style="font-weight: normal;font-size: 8pt;padding-top: 10px; " colspan="{{$colspan}}">
          YMPI is a Company that Quality Management System ISO 9001 : 2015 certified by the (BV) Bureau Veritas
        </th>
      </tr>
    </table>
    <img width="100%" style="float: bottom;margin-top: 25px" src="{{ url('/data_file/qa/bawah.png') }}">
  </div>
  <div style="width: 100%;page-break-before: always;border: 2px solid black;height: 708px">
    <table style="width: 100%;">
      <tr>
        <th style="font-weight: bold;font-size: 10pt;text-decoration: underline;text-align: center;padding-bottom: 10px;">
          HASIL SERTIFIKASI
        </th>
      </tr>
      <tr>
        <th>
          <table>
            <tr>
              <td style="font-size: 7pt;padding-left: 7px">
                Nama 
              </td>
              <td style="font-size: 7pt;padding-left: 7px">
                : {{strtoupper($datas[0]->name)}}
              </td>
            </tr>
            <tr>
              <td style="font-size: 7pt;padding-left: 7px">
                NIK 
              </td>
              <td style="font-size: 7pt;padding-left: 7px">
                : {{strtoupper($datas[0]->employee_id)}}
              </td>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <th style="font-size: 7.5;padding-top: 10px;padding-left: 7px;">
          A. POINT PENILAIAN :
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 23px">
          1. PEMAHAMAN IK
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px">
          * Standart Kelulusan : Penguasaan 100% Point IK
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px">
          * Nilai Aktual :
        </th>
      </tr>
      <tr>
        <th style="font-size: 6pt;padding-left: 60px;padding-top: 8px;">
          <table style="width: 60%">
            <tr>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">Total Point IK</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">OK</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">NG</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">% Nilai</th>
            </tr>
            <tr>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
            </tr>
            <tr>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[0]->question}}</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[0]->answer}}</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[0]->total_answer}}</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black;background-color: #ffdc7a">{{$datas[0]->presentase_total}} %</td>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px;padding-top: 8px">
          * Status : {{$datas[0]->decision}}
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 23px;padding-top: 10px">
          2. PENGUASAAN STANDART PRODUK
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px">
          * Standart Kelulusan :
        </th>
      </tr>
      <tr>
        <th style="font-size: 6.5pt;padding-left: 60px;padding-top: 8px;">
          Presentasi Nilai Grade A = 100%
        </th>
      </tr>
      <tr>
        <th style="font-size: 6.5pt;padding-left: 60px;">
          Presentasi Nilai Total >= 90%
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px;padding-top: 10px;">
          * Komposisi Kesalahan Maksimal yang diperbolehkan :
        </th>
      </tr>
      <tr>
        <th style="font-size: 6pt;padding-left: 60px;padding-top: 8px;">
          <table style="width: 95%">
            <tr>
              <th style="font-size: 6pt;text-align: right;border: 1px solid black;width: 4%;padding-right: 3px;">Grade Soal</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">A</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">B</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">C</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">D</th>
              <th rowspan="2" style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">Total Kesalahan</th>
            </tr>
           
            <tr>
              <td style="font-size: 6pt;text-align: right;border: 1px solid black;padding-right: 3px;">Skor Tiap Grade</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">4</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">3</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">2</td>
              <td style="font-size: 6pt;text-align: center;border: 1px solid black">1</td>
            </tr>
            <tr>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
            </tr>
            @foreach($composition as $com)
            <tr>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->composition}}</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->com_a}}</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->com_b}}</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->com_c}}</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->com_d}}</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$com->total_fault}}</th>
            </tr>
            @endforeach
          </table>
        </th>
      </tr>

      <tr>
        <th style="font-size: 7pt;padding-left: 35px;padding-top: 10px;">
          * Nilai Aktual :
        </th>
      </tr>
      <tr>
        <th style="font-size: 6pt;padding-left: 60px;padding-top: 8px;">
          <table style="width: 95%">
            <tr>
              <th rowspan="2" style="font-size: 6pt;text-align: center;border: 1px solid black;width: 4%;padding-right: 3px;">Keterangan</th>
              <th colspan="4" style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">Grade Soal</th>
              <th rowspan="2" style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">Total</th>
            </tr>
            <tr>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">A</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">B</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">C</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black;width: 2%">D</th>
            </tr>
            <tr>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
              <th style="border: 1px solid black"></th>
            </tr>
            <tr>
              <th style="font-size: 6pt;border: 1px solid black;padding-left: 2px;">1. Skor</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">4</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">3</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">2</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">1</th>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">-</th>
            </tr>
            <tr>
              <th style="font-size: 6pt;border: 1px solid black;padding-left: 2px;">2. Jumlah Soal</th>
              <?php for ($i=1; $i < count($datas); $i++) { ?>
                <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[$i]->question}}</th>
              <?php } ?>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[1]->total_question}}</th>
            </tr>
            <tr>
              <th style="font-size: 6pt;border: 1px solid black;padding-left: 2px;">3. Jumlah Soal Benar</th>
              <?php for ($j=1; $j < count($datas); $j++) { ?>
                <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[$j]->answer}}</th>
              <?php } ?>
              <th style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[1]->total_answer}}</th>
            </tr>
            <tr style="background-color: #ffdc7a">
              <th style="font-size: 6pt;border: 1px solid black;padding-left: 2px;">4. Presentase Nilai Grade A</th>
              <th colspan="5" style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[0]->presentase_a}} %</th>
            </tr>
            <tr style="background-color: #ffdc7a">
              <th style="font-size: 6pt;border: 1px solid black;padding-left: 2px;">5. Presentase Nilai Total</th>
              <th colspan="5" style="font-size: 6pt;text-align: center;border: 1px solid black">{{$datas[1]->presentase_total}} %</th>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <th style="font-size: 7pt;padding-left: 35px;padding-top: 8px">
          * Status : {{$datas[0]->decision}}
        </th>
      </tr>
      <tr>
        <th style="font-size: 7.5;padding-top: 10px;padding-left: 7px;">
          B. STATUS KELULUSAN
        </th>
      </tr>
      <tr>
        <th style="font-size: 6pt;padding-left: 60px;padding-top: 8px;">
          <table style="width: 40%">
            <tr>
              <th style="font-size: 7.5pt;text-align: center;border: 1.5px solid black;padding: 6px;">{{$datas[0]->decision}}</th>
            </tr>
          </table>
        </th>
      </tr>
      <tr>
        <th style="text-align: right;align-self: right;align-content: right;align-items: right;font-size: 5pt;">
          <table style="margin-right: 0px;margin-left: auto;padding-top: 60px;padding-right: -40px !important;">
            <tr>
            @foreach($data_approval_bawah as $databawah)
              <th style="border: 1px solid black;text-align: center;padding: 2px;font-size: 5pt;">{{$databawah->approver_header}}</th>
            @endforeach
            </tr>
            <tr>
            @foreach($data_approval_bawah as $databawah)
              <?php if ($databawah->approver_status == null){ ?>
                <th style="border: 1px solid black;text-align: center;padding: 2px;font-size: 5pt;"><br><br><br></th>
              <?php }else{ ?>
                <th style="border: 1px solid black;text-align: center;padding: 2px;font-size: 5pt;color: green">{{$databawah->approver_status}}<br>{{explode(' ', $databawah->approved_at)[0]}}<br>{{explode(' ', $databawah->approved_at)[1]}}</th>
              <?php } ?>
            @endforeach
            </tr>
            <tr>
            @foreach($data_approval_bawah as $databawah)
              @if(count(explode(' ',$databawah->approver_name)) > 0)
              <th style="border: 1px solid black;text-align: center;padding: 2px;font-size: 5pt;">{{$databawah->approver_name}}</th>
              @else
              <th style="border: 1px solid black;text-align: center;padding: 2px;font-size: 5pt;">{{explode(' ',$databawah->approver_name)[0]}} {{explode(' ',$databawah->approver_name)[1]}}</th>
              @endif
            @endforeach
            </tr>
          </table>
        </th>
      </tr>
    </table>
  </div>
</body>
</html>