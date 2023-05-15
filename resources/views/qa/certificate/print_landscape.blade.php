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
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body>
  <div style="width: 100%;">
    <table style="width: 100%;" id="tablePenilaian">
      <tr>
        <th style="font-weight: bold;font-size: 6pt;">
          NAMA
        </th>
        <th colspan="10" style="font-weight: bold;font-size: 6pt;padding-left: 20px">
          : {{strtoupper($datas[0]->name)}}
        </th>
      </tr>
      <tr>
        <th style="font-weight: bold;font-size: 6pt">
          NIK
        </th>
        <th colspan="10" style="font-weight: bold;font-size: 6pt;padding-left: 20px">
          : {{strtoupper($datas[0]->employee_id)}}
        </th>
      </tr>
      <tr>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          SUBJECT
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          JENIS TES
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          KATEGORI
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          JUMLAH SOAL
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          BOBOT NILAI TIAP GRADE
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          JUMLAH SOAL BENAR
        </th>
        <th colspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          STANDARD KELULUSAN
        </th>
        <th colspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          NILAI AKTUAL
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          STATUS KELULUSAN
        </th>
        <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
          CATATAN
        </th>
      </tr>
      <?php $subjects = []; ?>
      <?php for($i =0; $i < count($data_subject);$i++){ ?>
      <tr>
        <?php if ($i == (count($data_subject)-1)){?>
          <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
            {{$data_subject[$i]->subject}}
            <?php array_push($subjects,$data_subject[$i]->subject) ?>
          </th>
        <?php }else{ ?>
          <th rowspan="6" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
            {{$data_subject[$i]->subject}}
            <?php array_push($subjects,$data_subject[$i]->subject) ?>
          </th>
        <?php } ?>
        <?php for($j = 0; $j < count($datas);$j++){ ?>
        <?php $questionsubtotal = 0;$totalsubtotal = 0; ?>
          <?php if($datas[$j]->subject == $data_subject[$i]->subject){ ?>
            <?php if($j % 6 == 0){ ?>
              <?php if ($i == (count($data_subject)-1)){?>
                <th colspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                  {{$datas[$j]->category}}
                </th>
              <?php }else{ ?>
                <?php if($j % 6 ==  0){ ?>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    {{$datas[$j]->test_type}}
                  </th>
                <?php  } ?>
                <?php if($j % 6 ==  1){ ?>
                  <th rowspan="5" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    {{$datas[$j]->test_type}}
                  </th>
                <?php  } ?>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  {{$datas[$j]->category}}
                </th>
              <?php } ?>
              <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                {{($datas[$j]->question)}}
              </th>
              <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                {{($datas[$j]->weight)}}
              </th>
              <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                {{($datas[$j]->question_result)}}
              </th>

              <?php if ($i == (count($data_subject)-1)){?>

                <?php if($j % 4 ==  0 || $j % 4 ==  2){ ?>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                Presentase Nilai Total
                </th>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  100%
                </th>
                <?php  } ?>
                
                <?php if($j % 4 ==  0 || $j % 4 ==  2){ ?>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Nilai Total
                </th>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  <?php if($datas[$j]->presentase_result != null) {?>
                  {{($datas[$j]->presentase_result)}}%
                  <?php }?>
                </th>
                <?php  } ?>
                <?php if($j % 6 == 0){ ?>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                    {{($datas[$j]->result_grade)}}
                  </th>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                    {{($datas[$j]->note)}}
                  </th>
                <?php  } ?>
              <?php }else{ ?>
                <?php if($j % 6 == 0){ ?>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Point OK
                </th>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  100%
                </th>
                <?php  } ?>
                <?php if($j % 6 == 1){ ?>
                <th  style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Nilai Grade A
                </th>
                <th  style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  >=  95%
                </th>
                <?php  } ?>

                <?php if($j % 6 ==  2){ ?>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                Presentase Nilai Total
                </th>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  >=  90%
                </th>
                <?php  } ?>

                <?php if($j % 6 == 0){ ?>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Point OK
                </th>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  <?php if($datas[$j]->presentase_result != null) {?>
                  {{($datas[$j]->presentase_result)}}%
                  <?php }?>
                </th>
                <?php  } ?>

                <?php if($j % 6 == 1){ ?>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Nilai Grade A
                </th>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  <?php if($datas[$j]->presentase_a != null) {?>
                  {{$datas[$j]->presentase_a}}%
                  <?php }?>
                </th>
                <?php  } ?>
                
                <?php if($j % 6 ==  2){ ?>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Nilai Total
                </th>
                <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                  <?php if($datas[$j]->presentase_result != null) {?>
                  {{($datas[$j]->presentase_result)}}%
                  <?php }?>
                </th>
                <?php  } ?>
                <?php if($j % 6 == 0){ ?>
                  <th rowspan="6" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                    {{($datas[$j]->result_grade)}}
                  </th>
                  <th rowspan="6" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                    {{($datas[$j]->note)}}
                  </th>
                <?php  } ?>
              <?php } ?>

              
            <?php } ?>
          <?php } ?>

        <?php } ?>
      </tr>
        <?php for($j =1; $j < count($datas);$j++){ ?>
        <?php $questionsubtotal = 0;$totalsubtotal = 0; ?>
          <?php if($datas[$j]->subject == $data_subject[$i]->subject){ ?>
            <?php if($j % 6 != 0){ ?>
              <tr>
                <?php if ($i == (count($data_subject)-1)){?>
                  <th colspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                    {{$datas[$j]->category}}
                  </th>
                <?php }else{ ?>
                  <?php if($j % 6 ==  0){ ?>
                    <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                      {{$datas[$j]->test_type}}
                    </th>
                  <?php  } ?>
                  <?php if($j % 6 ==  1){ ?>
                    <th rowspan="5" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                      {{$datas[$j]->test_type}}
                    </th>
                  <?php  } ?>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    {{$datas[$j]->category}}
                  </th>
                <?php } ?>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                  {{($datas[$j]->question)}}
                </th>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                  {{($datas[$j]->weight)}}
                </th>
                <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                  {{($datas[$j]->question_result)}}
                </th>
                <?php if ($i == (count($data_subject)-1)){ ?>
                  <!-- <?php if($j % 4 ==  0 || $j % 4 ==  2){ ?>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                  Presentase Nilai Total
                  </th>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;font-weight: normal;">
                    100%
                  </th>
                  <?php  } ?> -->

                <?php }else{ ?>
                  <?php if($j % 6 == 0){ ?>
                  <th  style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    Presentase Point OK
                  </th>
                  <th  style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                    100%
                  </th>
                  <?php  } ?>
                  <?php if($j % 6 == 1){ ?>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    Presentase Nilai Grade A
                  </th>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                    >=  95%
                  </th>
                  <?php  } ?>
                  <?php if($j % 6 ==  2){ ?>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                  Presentase Nilai Total
                  </th>
                  <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                    >=  90%
                  </th>
                  <?php  } ?>

                  <?php if($j % 6 == 0){ ?>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    Presentase Point OK
                  </th>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                    {{($datas[$j]->presentase_result.'%')}}
                  </th>
                  <?php  } ?>

                  <?php if($j % 6 == 1){ ?>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                    Presentase Nilai Grade A
                  </th>
                  <th style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                    <?php if($datas[$j]->category == 'Grade A'){
                      if($datas[$j]->question != null){
                        echo (($datas[$j]->question_result/$datas[$j]->question)*100).'%';
                      }
                    } ?>
                  </th>
                  <?php  } ?>
                  
                  <?php if($j % 6 ==  2){ ?>
                    <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: left;font-weight: normal;">
                      Presentase Nilai Total
                    </th>
                    <th rowspan="4" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: right;font-weight: normal;">
                      <?php if($datas[$j]->presentase_result != null) {?>
                      {{($datas[$j]->presentase_result.'%')}}
                      <?php } ?>
                    </th>
                    <?php  } ?>
                  <?php if($j % 6 == 0){ ?>
                    <th rowspan="6" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                      {{($datas[$j]->result_grade)}}
                    </th>
                    <th rowspan="6" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
                      {{($datas[$j]->note)}}
                    </th>
                  <?php  } ?>
                <?php } ?>

              </tr>
            <?php } ?>
          <?php } ?>
        <?php } ?>
        <!-- <tr>
          <th rowspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
            Presentase Nilai Total
          </th>
          <th rowspan="2" style="font-weight: bold;font-size: 6.5pt;padding-left: 4px;padding-right:4px;border: 1px solid black;text-align: center;">
            >=  90%
          </th>
        </tr> -->
      <?php } ?>
      
    </table>
    <div style="width: 100%;padding-top: 20px;" align="right">
      <table align="right">
        <tr>
          @foreach($data_approval as $approval)
          <?php if ($approval->approver_header != null) { ?>
            <td style="border: 1px solid black;padding-right: 5px;padding-left: 5px;font-weight: bold;width: 100px">{{$approval->approver_header}}</td>
          <?php } ?>
          @endforeach
        </tr>

        <tr>
          @foreach($data_approval as $approval)
          <?php if ($approval->approver_header != null) { ?>
            <?php if ($approval->approved_at != null) { ?>
              <td style="border: 1px solid black;padding-right: 5px;padding-left: 5px;color: green;font-weight: bold;">Approved<br>{{explode(' ', $approval->approved_at)[0]}}<br>{{explode(' ', $approval->approved_at)[1]}}</td>
            <?php }else{ ?>
              <td style="border: 1px solid black;padding-bottom: 20px;padding-top: 20px;padding-right: 5px;padding-left: 5px"></td>
            <?php } ?>
          <?php } ?>
          @endforeach
        </tr>

        <tr>
          @foreach($data_approval as $approval)
          <?php if ($approval->approver_header != null) { ?>
            <?php if (count(explode(' ', $approval->approver_name)) > 1){ ?>
              <td style="border: 1px solid black;padding-right: 5px;padding-left: 5px;font-weight: bold;">{{explode(' ', $approval->approver_name)[0].' '.explode(' ', $approval->approver_name)[1]}}</td>
            <?php }else{ ?>
              <td style="border: 1px solid black;padding-right: 5px;padding-left: 5px;font-weight: bold;">{{$approval->approver_name}}</td>
            <?php } ?>
          <?php } ?>
          @endforeach
        </tr>
        <tr>
          @foreach($data_approval as $approval)
          <?php if ($approval->approver_header != null) { ?>
            <td style="border: 1px solid black;padding-right: 5px;padding-left: 5px;font-weight: bold;">{{$approval->remark}}</td>
          <?php } ?>
          @endforeach
        </tr>
      </table>
    </div>
  </div>
  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
</body>
</html>
