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
      border: 1px solid black !important;
      border-collapse: collapse;
    }

    .centera{
      text-align: center;
      vertical-align: middle !important;
      width: 1%;
    }

    /*table {
      page-break-inside: auto;
    }*/

    /*.page-break {
        page-break-after: always;
    }*/

    @page { margin: 100px 50px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body>
  <div class="footer">

      Page <span class="pagenum"></span>
  </div>
  <table style="width: 100%;padding-left: 10pt">
    <thead>
      <tr>
        <td class="centera">
          <img width="120" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important;">
        </td>
        <td colspan="4" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold;width: 20%">AUDIT NG JELAS</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="5" style="font-size: 18px;text-align: center;">
          <table>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Claim Title</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$details[0]->audit_title}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Auditor</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$details[0]->auditor}} - {{$details[0]->name}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Audited At</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$details[0]->created_at}}</td>
            </tr>
          </table>
        </td>
      </tr>
          <?php $count = 1;
              $count2 = 0; ?>
                <?php $index = 1; ?>
                @foreach($details as $det)
                <?php $count2 += 1; ?>
                <?php if ($count == 1) { ?>
                  <tr>
                  <td colspan="5" style="text-align: center;">
                  <!-- <div class="col-xs-12 col-md-12 col-lg-12"> -->
                    <table style="width: 100%">
                      <thead>
                        <tr align="center">
                          <th style="text-align: center;width: 1%">#</th>
                          <th style="text-align: center;width: 2%">Point Check</th>
                          <th style="text-align: center;width: 3%">Foto Referensi</th>
                          <th style="text-align: center;width: 1%">Kondisi</th>
                          <th style="text-align: center;width: 3%">Foto Aktual</th>
                          <th style="text-align: center;width: 2%">Catatan</th>
                          <th style="text-align: center;width: 2%">Penanganan Dari Genba</th>
                        </tr>
                      </thead>
                      <tbody style="border:1px solid black">
                <?php } ?>
                <tr>
                 <td style="text-align: center;">{{$index}}</td>
                 <td style="padding: 2px;padding-left: 5px"><?php echo $det->point_check ?></td>
                  <?php if ($det->audit_images != 'null') { ?>
                   <td style="text-align:center;"><img width="125px" src="{{ url('/data_file/qa/ng_jelas_point') }}/{{$det->audit_images}}"></td>
                  <?php } else { ?>
                   <td></td>
                  <?php  } ?>
                  <?php if ($type == 'QA') { ?>
                    <?php if ($det->kondision == 'OK') { ?>
                     <td style="text-align:center;background-color:#a2ff8f">OK</td>
                    <?php } else if ($det->kondision == 'NG') { ?>
                     <td style="text-align:center;background-color:#ff8f8f">NG</td>
                    <?php } else if ($det->kondision == 'NS'){ ?>
                     <td style="text-align:center;background-color:#fff68f">Observ</td>
                    <?php } else{ ?>
                     <td>-</td>
                    <?php } ?>
                   <td>
                    <?php if (str_contains($det->images,',')){ ?>
                      <?php $imagesss = explode(',', $det->images) ?>
                      <?php for ($i=0; $i < count($imagesss); $i++) { ?>
                        <img width="125px" src="{{ url('/data_file/qa/ng_jelas/') }}/{{$imagesss[$i]}}">
                        <br>
                      <?php } ?>
                    <?php }else { ?>
                      <img width="125px" src="{{ url('/data_file/qa/ng_jelas/') }}/{{$det->images}}">
                    <?php } ?>
                   </td>
                  <?php }else{ ?>
                    <?php if ($det->kondision == 'Good') { ?>
                     <td style="text-align:center;background-color:#a2ff8f">OK</td>
                    <?php } else if ($det->kondision == 'Not Good') { ?>
                     <td style="text-align:center;background-color:#ff8f8f">NG</td>
                    <?php } else{ ?>
                     <td>-</td>
                    <?php } ?>
                   <td style="text-align:center;"><img width="125px" src="{{ url('/data_file/') }}/{{$det->images}}"></td>
                  <?php } ?>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->note ?></td>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->handling ?></td>
                </tr>
                <?php $count += 1; ?>
                <?php if ($count2 % 2 == 0): ?>
                  <?php $count = 1; ?>
                  </tbody>
                </table>
              </td>
              </tr>
              <!-- </div> -->
                <?php endif ?>
                <?php $index++ ?>
                @endforeach
    </tbody>
  </table>
  
</body>
</html>