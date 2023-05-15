<html>
<body>
  <div>
    <center>
      <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
      <p>This is an automatic notification. Please do not reply to this address. 自動メールです。返信しないでください。</p>
      <span style="font-weight: bold; color: purple; font-size: 24px;">Permohonan Loading Limbah WWT</span><br><br>
    </center>
  </div>     
  <?php for ($i=0; $i < count($data['resumes']); $i++) { 
    $b = explode(',', $data['resumes'][$i]->jenis);
    $c = explode(',', $data['resumes'][$i]->quantity);
    $d = explode(',', $data['resumes'][$i]->slip);
    $e = explode(',', $data['resumes'][$i]->kode_limbah);
    $no = 1;
    if ($i == 0) {
      ?>
      <div class="col-xs-12">
        <table class="table table-bordered" style="width: 100%; font-family: arial; border-collapse: collapse; text-align: center" cellspacing="0">
          <tbody align="center">
            <?php
          }
          for ($z=0; $z < count($data['resumes']); $z++) {
            if ($z == $i) {
              if ($z == 0 || ($z+1) % 2 == 1) 
                echo '<tr>';
                print_r('<th style="width: 1%; vertical-align: top">
                <table style="border-collapse: collapse; width: 100%;">
                <thead>
                <tr align="center">
                <th colspan="4" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Limbah '.$b[0].' ('.$e[0].')</th>
                </tr>
                <?php $no = 1 ?>
                <tr align="center"> 
                <td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">NO. LIMBAH</td>
                <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">SLIP</td>
                <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">BERAT</td>
                <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">JML. JUMBO BAG</td>
                </tr>

                <tbody id="bodyTableOutstanding">');


                for ($a=0; $a < count($b); $a++) { ?>
                  <?php 
                  print_r('
                    <tr align="center"> 
                    <td style="border:1px solid black; font-size: 13px; height: 20;">'.$no++.'</td>
                    <td style="border:1px solid black; font-size: 13px; height: 20;">'.$d[$a].'</td>
                    <td style="border:1px solid black; font-size: 13px; height: 20;">'.$c[$a].' KG</td>
                    <td style="border:1px solid black; font-size: 13px; height: 20;">1</td>
                    </tr>');

                  } ?>

                  <?php print_r('
                    <tr align="center"> 
                    <td colspan="2" style="border:1px solid black; font-size: 13px; height: 20;">JUMLAH</td>
                    <td style="border:1px solid black; font-size: 13px; height: 20;">'.$data['resumes'][$i]->jumlah.' KG</td>
                    <td style="border:1px solid black; font-size: 13px; height: 20;">'.$data['resumes'][$i]->banyak.'</td>
                    </tr>
                    </tbody>
                    </thead>
                    </table>
                    </th>');
                  if (($z+1) == count($data['resumes'])) { echo '</tr>'; }
                }
              }
              if (($i+1) == count($data['resumes'])) { ?>
              </tbody>            
            </table>
          <?php  } } ?>
          <center>
            <br>
            <br>
            Apakah anda akan menyetujui permintaan ini? (こちらの申請を承認しますか。)
            <br>
            <table style="width: 100%">
              <tr>
                <th style="width: 10%; font-weight: bold; color: black;">
                  <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"  
                  href=" {{ url('verivikasi/email/wwt/approve/'.$data['approval'][0]->slip_disposal.'/'.$data['approval'][0]->approver_id) }} ">&nbsp; Approve(承認) &nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                  <a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size:20px;" href=" {{ url('verivikasi/email/wwt/reject/'.$data['approval'][0]->slip_disposal.'/'.$data['approval'][0]->approver_id) }} ">&nbsp;&nbsp;&nbsp; Reject(却下) &nbsp;&nbsp;&nbsp;</a>
                </th>
              </tr>
            </table>
            <br>
            <br>
            <br>
            <table class="table table-bordered" style="width: 30%; font-family: arial; border-collapse: collapse; text-align: center;" cellspacing="0">
              <tr>
                <th style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40px; background-color:  #e8daef; width: 50px ">{{$data['isi_approval'][0]->remark}}</th>
                <th style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40px; background-color:  #e8daef; width: 50px ">{{$data['isi_approval'][1]->remark}}</th>
              </tr>
              <tr style="height: 50px">
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 50px">
                  @if($data['isi_approval'][0]->status == 'Approve')
                  <img style="width: 70px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/wwt/PI1210001.png')))}}">
                  @endif
                  <br><span>{{ $data['isi_approval'][0]->approved_at }}</span></td>
                  <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 50px">
                    @if($data['isi_approval'][1]->status == 'Approve')
                    <img style="width: 70px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/wwt/PI1404002.png')))}}">
                    @endif
                    <br><span>{{ $data['isi_approval'][1]->approved_at }}</span></td>
                  </tr>
                  <tr>
                    <td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30px; background-color:  #e8daef">{{ $data['isi_approval'][0]->approver_name }}</td>

                    <td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30px; background-color:  #e8daef">{{ $data['isi_approval'][1]->approver_name }}</td>
                  </tr>
                </table>
                <p>
                  <b>Thanks & Regards,</b>
                </p>
                <p>PT. Yamaha Musical Products Indonesia<br>
                  Jl. Rembang Industri I / 36<br>
                  Kawasan Industri PIER - Pasuruan<br>
                  Phone   : 0343 – 740290<br>
                  Fax.    : 0343 - 740291
                </p>
              </center>
            </div>
          </body>
          </html>