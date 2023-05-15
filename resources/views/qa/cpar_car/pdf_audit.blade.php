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
    #tableCheck > tbody > tr > td > img {
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
        <td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold;width: 20%">AUDIT CPAR & CAR</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="6" style="font-size: 18px;text-align: center;">
          <table>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Claim Title</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->audit_title}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Product</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->product}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Area</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->area}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Department</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->department}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Auditor</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{$audit[0]->auditor}} - {{$audit[0]->auditor_name}}</td>
            </tr>
            <tr>
              <td style="padding: 5px;padding-left: 10px;border: 0px !important;font-size: 10px;">Auditee</td>
              <td style="border: 0px !important;font-size: 10px;"> : </td>
              <td style="border: 0px !important;font-size: 10px;font-weight: bold">{{explode('_',$foreman)[0]}} - {{explode('_',$foreman)[1]}}</td>
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
                          <th style="text-align: center;width: 2%">Point Check</th>
                          <th style="text-align: center;width: 2%">Standard</th>
                          <th style="text-align: center;width: 1%">Kondisi</th>
                          <th style="text-align: center;width: 2%">Evidence</th>
                          <th style="text-align: center;width: 2%">Note</th>
                          <th style="text-align: center;width: 2%">Penanganan Dari Genba</th>
                        </tr>
                      </thead>
                      <tbody style="border:1px solid black">
                <?php } ?>
                <tr>
                 <td style="text-align: center;">{{$det->audit_index}}</td>
                 <td style="padding: 2px;padding-left: 5px"><?php echo $det->audit_point ?></td>
                 @if($det->audit_images != null)
                  <td style=""><img src="{{url('data_file/qa/ng_jelas_point/'.$det->audit_images)}}" class="user-image" alt="User image"></td>
                  @else
                  <td style=""></td>
                  @endif
                  <?php if ($det->result_check == 'OK') { ?>
                     <td style="text-align:center;background-color:#a2ff8f">OK</td>
                    <?php } else if ($det->result_check == 'NG') { ?>
                     <td style="text-align:center;background-color:#ff8f8f">NG</td>
                    <?php } ?>
                 <td style=""><img src="{{url('data_file/qa/cpar_car/'.$det->result_image)}}" class="user-image" alt="User image"></td>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->note ?></td>
                  <td style="padding: 2px;padding-left: 5px"><?php echo $det->handling ?></td>
                </tr>
                <?php $count += 1; ?>
                <?php if ($count2 % 10 == 0): ?>
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