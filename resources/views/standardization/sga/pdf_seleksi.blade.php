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
        src: url("{{ storage_path('data_file\calibri.ttf') }}");
        font-style: normal;
    }

    #tablePenilaian > tr > th{
      padding-left: 3px;
    }

    @page { margin: 20px 20px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: 180px; right: 0px; text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body>
  <div style="width: 100%;">
    <table style="width: 100%;" id="tablePenilaian">
      <thead>
        <tr>
          <th colspan="{{4+count($sga_asesor)}}" style="font-weight: bold;font-size: 12pt;text-align: center;border:1px solid black">
            Hasil Seleksi SGA {{str_replace("_"," ",$periode)}}
          </th>
        </tr>
        <tr>
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 1%">No. Urut</th>
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 2%">Nama Team</th>
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 5%">Bagian</th>
          <?php $asesor_id = []; ?>
          @foreach($sga_asesor as $asesor)
          @if(count(explode(' ',$asesor->asesor_name)) > 1)
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 3%">{{explode(' ',$asesor->asesor_name)[0]}} {{explode(' ',$asesor->asesor_name)[1]}}</th>
          @else
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 3%">{{explode(' ',$asesor->asesor_name)[0]}}</th>
          @endif
          <?php array_push($asesor_id, $asesor->asesor_id) ?>
          @endforeach
          <th style="font-weight: bold;font-size: 8pt;text-align: center;border:1px solid black;width: 2%">Total Penilaian</th>
        </tr>
      </thead>
      <tbody>
        <?php $index = 1; ?>
        @foreach($teams as $teams)
        <?php if ($index < 6) {
          $color = '#c3e157';
        }else{
          $color = 'none';
        } ?>
        <tr style="background-color: {{$color}}">
          <td style="font-size: 8pt;border:1px solid black;text-align: right;padding: 3px;">{{$index}}</td>
          <td style="font-size: 8pt;border:1px solid black;padding: 3px;">{{$teams->team_no}}</td>
          <td style="font-size: 8pt;border:1px solid black;padding: 3px;">{{$teams->team_name}}</td>
          <?php $total = 0; ?>
          <?php for ($i=0; $i < count($asesor_id); $i++) { ?>
            <?php for ($j=0; $j < count($sga_result); $j++) { 
              if ($sga_result[$j]->team_no == $teams->team_no && $sga_result[$j]->asesor_id == $asesor_id[$i]) { ?>
                <td style="font-size: 8pt;border:1px solid black;padding: 3px;text-align: right;">{{$sga_result[$j]->total_nilai}}</td>
                <?php $total = $total +$sga_result[$j]->total_nilai?>
              <?php }?>
            <?php } ?>
          <?php } ?>
          <td style="font-size: 8pt;border:1px solid black;padding: 3px;text-align: right;">{{$total}}</td>
        </tr>
        <?php $index++ ?>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="width: 100%;padding-top: 20px;" class="footer" align="right">
      <table align="right">
        <tr>
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Dibuat Oleh</td>
          <?php $index_juri = 1; ?>
          @foreach($sga_asesor as $asesor)
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Juri {{$index_juri}}</td>
          <?php $index_juri++ ?>
          @endforeach
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Disetujui Oleh</td>
        </tr>
        <tr>
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->secretariat_approver_status == null)
            <br><br><br><br><br><br>
            @else
            Approved<br>
            <?php echo $teams_all[0]->sec_status ?>
            @endif
          </td>
            @foreach($sga_asesor as $asesor)
            <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">Approved<br><?php echo $asesor->created_at ?></td>
            @endforeach
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;color: green;font-weight: bold;">
            @if($teams_all[0]->dgm_approver_status == null)
            <br><br><br><br><br><br>
            @else
            <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/files/ttd_pr_po/stempel_pak_budhi.jpg') }}">
            <!-- <span style="position: absolute;width:75px;font-size: 8px;top:537px;left:673px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($teams_all[0]->dgm_status))}}</span> -->
            <span style="position: absolute;width:75px;font-size: 8px;top:76.5px;left:674px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($teams_all[0]->dgm_status))}}</span>
            @endif
          </td>
        </tr>
        <tr>
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Rani N. S.</td>
            @foreach($sga_asesor as $asesor)
            @if(count(explode(' ',$asesor->asesor_name)) > 1)
            <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$asesor->asesor_name)[0]}} {{explode(' ',$asesor->asesor_name)[1]}}</td>
            @else
            <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">{{explode(' ',$asesor->asesor_name)[0]}}</td>
            @endif
            @endforeach
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Budhi Apriyanto</td>
        </tr>
        <tr>
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Secretariat</td>
            @foreach($sga_asesor as $asesor)
            <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">Manager</td>
            @endforeach
          <td style="font-size: 8pt;border:1px solid black;text-align: center;padding: 3px;">DGM Prod. Div.</td>
        </tr>
      </table>
    </div>
  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
</body>
</html>
