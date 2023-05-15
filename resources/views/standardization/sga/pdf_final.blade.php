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

    #tablePenilaian > tr > th{
      padding-left: 10px;
    }

    @page { margin: 20px 20px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body>
  <div style="width: 100%;">
    <table style="width: 100%;" id="tablePenilaian">
      <thead>
        <tr>
          <th colspan="9" style="font-weight: bold;font-size: 12pt;text-align: center;border:1px solid black;padding: 10px">
            Hasil Penilaian Akhir SGA {{str_replace('_',' ',str_replace("_Final"," ",$periode))}} PT Yamaha Musical Products Indonesia
          </th>
        </tr>
        <tr>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 1%">Juara</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%">Nama Team</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 5%">Bagian</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%">Hasil Penilaian Seleksi</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%">Hasil Penilaian Final</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%">40% Seleksi</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%">60% Final</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 2%;background-color: #e8e8e8">Total Penilaian</th>
          <th style="font-weight: bold;font-size: 10pt;text-align: center;border:1px solid black;width: 3%;background-color: #e8e8e8">Hadiah</th>
        </tr>
      </thead>
      <tbody>
        <?php $index = 1; ?>
        <?php $total_all = 0; ?>
        @foreach($teams as $teams)
        <tr>
          <td style="font-size: 10pt;border:1px solid black;text-align: right;padding: 10px;">{{$index}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;">{{$teams->team_no}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;">{{$teams->team_name}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;text-align: right;">{{$teams->total_nilai_seleksi}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;text-align: right;">{{$teams->total_nilai_final}}</td>
          <?php $total = 0; ?>
          <?php $seleksi = 0.4 * $teams->total_nilai_seleksi ?>
          <?php $final = 0.6 * $teams->total_nilai_final ?>
          <?php $total = $seleksi + $final ?>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;text-align: right;">{{$seleksi}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;text-align: right;">{{$final}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;text-align: right;background-color: #e8e8e8">{{$total}}</td>
          <td style="font-size: 10pt;border:1px solid black;padding: 10px;background-color: #e8e8e8"><span style="float: left">Rp.</span><span style="float: right">{{number_format(str_replace(',', '', $teams->hadiah),2,",",".")}}</span></td>
          <?php $hadiah_new = str_replace(',', '', $teams->hadiah);
          $total_all = $total_all + $hadiah_new; ?>
        </tr>
        <?php $index++ ?>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="8" style="font-size: 10pt;border:1px solid black;padding: 10px;font-weight: bold;text-align: right;">TOTAL</th>
          <th colspan="" style="font-size: 10pt;border:1px solid black;padding: 10px;font-weight: bold;background-color: #e8e8e8"><span style="float: left">Rp.</span><span style="float: right"><?php echo number_format($total_all,2,",",".") ?></span></th>
        </tr>
      </tfoot>
    </table>
    <br>
    <br>
    <div style="width: 100%;padding-top: 20px;" align="right">
      <table align="right">
        <tr>
          <td colspan="5" style="font-size: 10pt;">
            Pasuruan, <?php echo date('d F Y') ?>
          </td>
        </tr>
        <tr>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;width: 10%">Dibuat Oleh</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;width: 10%">Dicek Oleh</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;width: 10%">Dicek Oleh</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;width: 10%">Dicek Oleh</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;width: 10%">Disetujui Oleh</td>
        </tr>
        <tr>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->sec_status == null)
            <br><br>
            @else
            Approved<br>
            <?php echo $teams_all[0]->sec_status ?>
            @endif
          </td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->manager_qa_status == null)
            <br><br>
            @else
            Approved<br>
            <?php echo $teams_all[0]->manager_qa_status ?>
            @endif
          </td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->dgm_status == null)
            <br><br>
            @else
            <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/files/ttd_pr_po/stempel_pak_budhi.jpg') }}">
            <span style="position: absolute;width:75px;font-size: 8px;top:466px;left:750px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($teams_all[0]->dgm_status))}}</span>
            @endif
          </td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->gm_status == null)
            <br><br>
            @else
            <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/files/ttd_pr_po/stempel_pak_hayakawa.jpg') }}">
            <span style="position: absolute;width:75px;font-size: 8px;top:466px;left:865px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($teams_all[0]->gm_status))}}</span>
            @endif
          </td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->presdir_status == null)
            <br><br>
            @else
            <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_ichimura.png') }}">
            <span style="position: absolute;width:75px;font-size: 8px;top:466px;left:985px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($teams_all[0]->presdir_status))}}</span>
            @endif
          </td>
        </tr>
        <tr>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">Secretariat SGA</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">Manager QA</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">DGM Prod. Div.</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">GM Prod. Div.</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">President Director</td>
        </tr>
        <tr>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$teams_all[0]->secretariat_approver_name)[0]}} {{explode(' ',$teams_all[0]->secretariat_approver_name)[1]}}</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$teams_all[0]->manager_qa_approver_name)[0]}} {{explode(' ',$teams_all[0]->manager_qa_approver_name)[1]}}</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$teams_all[0]->dgm_approver_name)[0]}} {{explode(' ',$teams_all[0]->dgm_approver_name)[1]}}</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$teams_all[0]->gm_approver_name)[0]}} {{explode(' ',$teams_all[0]->gm_approver_name)[1]}}</td>
          <td style="font-size: 10pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$teams_all[0]->presdir_approver_name)[0]}} {{explode(' ',$teams_all[0]->presdir_approver_name)[1]}}</td>
        </tr>
      </table>
    </div>
  </div>
  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
</body>
</html>
