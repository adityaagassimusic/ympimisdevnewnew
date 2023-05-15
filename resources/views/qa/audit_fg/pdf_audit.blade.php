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

    @page { margin: 10px 20px; }
        .header { position: fixed; left: 0px; top: -50px; right: 0px; height: 50px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -30px; right: 0px; height: 30px;text-align: center;}
    #tableCheck > tbody > tr > td > p > img {
      width: 100px !important;
    }
  </style>
</head>
<body>
  <table style="width: 100%;padding-left: 10pt">
    <thead>
      <tr>
        <td class="centera">
          <img width="120" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important;">
        </td>
        <td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold;width: 20%">AUDIT FG / KD EI</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="6" style="font-size: 18px;text-align: center;">
          <table>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Product</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->product}}</td>
            </tr>
            <!-- <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Material</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">
                <?php $material_number = explode(',',$audit[0]->material_number) ?>
                <?php $material_description = explode(',',$audit[0]->material_description) ?>
                <?php $material = []; ?>
                <?php for($i = 0; $i < count($material_number);$i++){
                  array_push($material,$material_number[$i].' - '.$material_description[$i]);
                }
                echo join('<br>',$material); ?>
              </td>
            </tr> -->
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Material Audited</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">
                {{$audit[0]->material_audited}}
              </td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Qty Lot</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->qty_lot}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Qty Check</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->qty_check}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Auditor</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->auditor_id}} - {{$audit[0]->auditor_name}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Qty Auditor</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->qty_auditor}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Auditee</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->auditee_id}} - {{$audit[0]->auditee_name}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Audited At</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->created_at}}</td>
            </tr>
          </table>
        </td>
      </tr>
              <?php $count = 1;
              $count2 = 0; ?>
                <?php $index = 1; ?>
                @foreach($audit as $det)
                <?php $count2 += 1; ?>
                <?php if ($count == 1) { ?>
                  <tr>
                  <td colspan="6" style="text-align: center;">
                  <!-- <div class="col-xs-12 col-md-12 col-lg-12"> -->
                    <table style="width: 100%" id="tableCheck">
                      <thead>
                        <tr align="center">
                          <th style="text-align: center;width: 1%">#</th>
                          <th style="text-align: center;width: 2%">Urutan</th>
                          <th style="text-align: center;width: 2%">Point Check</th>
                          <th style="text-align: center;width: 2%">Standard</th>
                          <th style="text-align: center;width: 1%">NG</th>
                          <th style="text-align: center;width: 4%">NG Detail</th>
                          <th style="text-align: center;width: 2%">Note</th>
                          <th style="text-align: center;width: 2%">Penanganan Dari Genba</th>
                        </tr>
                      </thead>
                      <tbody style="border:1px solid black">
                <?php } ?>
                <tr>
                 <td style="text-align: center;">{{$det->ordering}}</td>
                 <td style="padding: 2px;padding-left: 5px"><?php echo $det->point_check ?></td>
                 <td style="padding: 2px;padding-left: 5px"><?php echo $det->point_check_details ?></td>
                 <td style="padding: 2px;padding-left: 5px"><?php echo $det->standard ?></td>
                 @if($det->qty_ng > 0)
                 <td style="padding: 2px;padding-right: 5px;text-align: right;font-size:20px;background-color: #ffb3b3"><?php echo $det->qty_ng ?></td>
                 @else
                 <td style="padding: 2px;padding-right: 5px;text-align: right;font-size:20px;"><?php echo $det->qty_ng ?></td>
                 @endif
                  <td style="padding: 2px;padding-left: 5px">
                    @if($det->ng_detail != '______')
                    Nama NG : <?php echo explode('_', $det->ng_detail)[0] ?><br>
                    Area : <?php echo explode('_', $det->ng_detail)[1] ?><br>
                    Pallet : <?php echo explode('_', $det->ng_detail)[2] ?><br>
                    Baris : <?php echo explode('_', $det->ng_detail)[3] ?><br>
                    Box : <?php echo explode('_', $det->ng_detail)[4] ?><br>
                    Line : <?php echo explode('_', $det->ng_detail)[5] ?><br>
                    Emp : <?php if (explode('_', $det->ng_detail)[6] != ''): ?>
                      <?php echo explode('_', $det->ng_detail)[6].' - '.explode('_', $det->ng_detail)[7]; ?>
                    <?php endif ?>
                    @endif
                  </td>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->note ?></td>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->handling ?></td>
                </tr>
                <?php $count += 1; ?>
                <?php if ($count2 % 7 == 0): ?>
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