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
<body style="border: 6px solid #aa8000;outline: 2px solid #ffbf00;">
  <div style="width: 100%;">
    <?php $approvals = []; ?>
    <?php for ($i=0; $i < count($data_approval); $i++) {  
      array_push($approvals, $data_approval[$i]->remark);
    } ?>
    <?php if (str_contains(join(",",$approvals),'Manager In Charge QA')) { 
      $colspan = '4';
    }else{
      $colspan = '3';
    }?>
    <table style="width: 100%;">
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 50px"><img width="200px" src="{{ url('/data_file/yamaha.png') }}"></th>
      </tr>
       <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 20px;font-weight: bold;font-size: 14pt">
          PT. YAMAHA MUSICAL PRODUCTS INDONESIA ( PT. YMPI )
          <hr style="border: 0.5pt solid #4f81bd;width: 80%;padding: 0px;margin-top: 0px;margin-bottom: 0px">
          <span style="font-weight: normal;font-size: 16pt;margin-top: 0px;padding-top: 0px">Commitment to Competence</span>
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 20px;font-weight: normal;font-size: 48pt">
          Certificate
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 16pt">
          To Whom It May Concern :
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 30pt">
          {{strtoupper($datas[0]->name)}}
          <hr style="border: 0.5pt solid #4f81bd;width: 70%;padding: 0px;margin-top: 0px;margin-bottom: 0px">
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 13pt;padding-top: 10px">
          Employee ID . {{strtoupper($datas[0]->employee_id)}}
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;padding-top: 0px;font-weight: normal;font-size: 13pt">
          PT. YMPI, {{$datas[0]->certificate_name}}
        </th>
      </tr>
      <tr>
        <!-- <th colspan="{{$colspan}}" style="text-align: center;padding-top: 100px; transform: rotate(270deg);"> -->
        <th colspan="{{$colspan}}" style="text-align: center;margin: -3% 0 0 0 !important;">
          <div style="overflow: hidden;">
            <img width="150px" style="margin: -2% 0 0 0 !important;" style="" src="{{ url('/images/avatar/'.$datas[0]->employee_id.'.jpg') }}">
          </div>
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 14pt;font-weight: normal;padding-top: 15px">
          Has the competence as an Inspector to check
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 16pt;font-weight: normal;">
          {{($datas[0]->description)}}
        </th>
      </tr>
      <tr>
        <th colspan="{{$colspan}}" style="text-align: center;font-size: 12pt;font-weight: normal;padding-top: 20px;padding-bottom: 5pt">
          Approved by,
        </th>
      </tr>
      <tr>
        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
          <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;">PT. YMPI</div>
        </th>

        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
          <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;">PT. YMPI</div>
        </th>

        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
          <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;">PT. YMPI</div>
        </th>
        <?php if (str_contains(join(",",$approvals),'Manager In Charge QA')) { ?>
        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
          <div style="text-align: center;padding-bottom: 0px;margin-bottom: 0px;">PT. YMPI</div>
        </th>
        <?php } ?>
      </tr>
      <tr>
        <?php if (str_contains(join(",",$approvals),'Manager In Charge QA')) { ?>
          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->remark == 'Manager In Charge QA') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_hayashi.png') }}">
                  <span style="position: absolute;width:75px;font-size: 8px;top:817.5px;left:51px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->remark == 'Manager STD') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_bu_yayuk.png') }}">
                  <span style="position:absolute;width:75px;font-size: 8px;top:817.5px;left:230px;color: #f84c32;font-family: arial-narrow;">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->remark == 'Director') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="69" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_arief.png') }}">
                  <span style="position: absolute;width:75px;font-size: 8px;top:817.5px;left:412px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->remark == 'President Director') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="69" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_ichimura.png') }}">
                  <span style="position: absolute;width:75px;font-size: 8px;top:817.5px;left:592px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>
        <?php }else{ ?>
          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align: middle;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->approver_id == 'PI0109004') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/files/ttd_pr_po/stempel_pak_budhi.jpg') }}">
                  <span style="position: absolute;width:75px;font-size: 8px;top:817.5px;left:80px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->approver_id == 'PI1206001') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="70" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_hayakawa.png') }}">
                  <span style="position:absolute;width:75px;font-size: 8px;top:817.5px;left:321px;color: #f84c32;font-family: arial-narrow;">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;">
            <?php for ($i=0; $i < count($data_approval); $i++) {  
              if ($data_approval[$i]->approver_id == 'PI2111044') { ?>
                <?php if ($data_approval[$i]->approver_status != null) { ?>
                  <img width="69" style="padding-top: 10px;padding-bottom: 0px;margin-bottom:0px;vertical-align: middle;" src="{{ url('/data_file/qa/ttd/stempel_pak_ichimura.png') }}">
                  <span style="position: absolute;width:75px;font-size: 8px;top:817.5px;left:562px;color: #f84c32;font-family: arial-narrow">{{date('d F Y',strtotime($data_approval[$i]->approved_at))}}</span>
                <?php }else{ ?>
                  <br>
                  <br>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </th>
        <?php } ?>
        
      </tr>
      <tr>
        <?php if (str_contains(join(",",$approvals),'Manager In Charge QA')) { ?>
        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;padding-top: 0px">
          <span style="text-decoration: underline;text-align: center;padding-top: 0px;margin-top: 0px">Toshiki Hayashi</span><br>
          <span style="padding-top: 0px;margin-top: 0px;text-align: center;">Manager In Charge QA</span>
        </th>

        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;">
          <span style="text-decoration: underline;text-align: center;">Yayuk Wakyuni</span><br>
          <span style="padding-top: 0px;margin-top: 0px;text-align: center;">Manager STD</span>
        </th>

        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;">
          <span style="text-decoration: underline;text-align: center;">Arief Soekamto</span><br>
          <span style="padding-top: 0px;margin-top: 0px;text-align: center;">Director</span>
        </th>
        <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;">
          <span style="text-decoration: underline;text-align: center;">Hiromichi Ichimura</span><br>
          <span style="padding-top: 0px;margin-top: 0px;text-align: center;">President Director</span>
        </th>
        <?php }else{ ?>
          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;padding-top: 0px">
            <span style="text-decoration: underline;text-align: center;padding-top: 0px;margin-top: 0px">Budhi Apriyanto</span><br>
            <span style="padding-top: 0px;margin-top: 0px;text-align: center;">Deputy General Manager</span>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;">
            <span style="text-decoration: underline;text-align: center;">Yukitaka Hayakawa</span><br>
            <span style="padding-top: 0px;margin-top: 0px;text-align: center;">General Manager</span>
          </th>

          <th style="font-size: 12pt;font-weight: normal;text-align: center;width: 1%;vertical-align:top;">
            <span style="text-decoration: underline;text-align: center;">Hiromichi Ichimura</span><br>
            <span style="padding-top: 0px;margin-top: 0px;text-align: center;">President Director</span>
          </th>
        <?php } ?>
      </tr>
      <tr>
        <th style="font-size: 11pt;width: 1%;padding-left: 50px;padding-top: 15px">
          Issued Date
        </th>
        <th style="font-size: 11pt;width: 1%;padding-top: 15px">
          : {{date('d F Y',strtotime($datas[0]->periode_from))}}
        </th>
        <th style="font-size: 11pt;width: 1%;padding-top: 15px">
          
        </th>
      </tr>
      <tr>
        <th style="font-size: 11pt;width: 1%;padding-left: 50px">
          Expired Date
        </th>
        <th style="font-size: 11pt;width: 1%;">
          : {{date('d F Y',strtotime($datas[0]->periode_to))}}
        </th>
        <th style="font-size: 11pt;width: 1%;">
          
        </th>
      </tr>
      <tr>
        <th style="font-size: 11pt;width: 1%;padding-left: 50px">
          Certificate No.
        </th>
        <th style="font-size: 11pt;width: 1%;">
          : {{strtoupper($datas[0]->certificate_code)}}
        </th>
        <th style="font-size: 11pt;width: 1%;">
          
        </th>
      </tr>
      <tr>
        <th style="font-weight: normal;font-size: 11pt;padding-top: 10px;text-align:center;" colspan="{{$colspan}}">
          YMPI is a Company that Quality Management System ISO 9001 : 2015 certified by the (BV) Bureau Veritas
        </th>
      </tr>
    </table>
  </div>
</body>
</html>