<!DOCTYPE html>
<html>
<head>
  <style type="text/css">
    td{
      padding-right: 5px;
      padding-left: 5px;
      padding-top: 0px;
      padding-bottom: 0px;
    }
    th{
      padding-right: 5px;
      padding-left: 5px;      
    }
    .button {
      background-color: #4CAF50; /* Green */
      border: none;
      color: white;
      padding: 10px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-sipx;
      cursor: pointer;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div>

    <center>
      <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><br>
      <span style="font-weight: bold; color: purple; font-size: 24px;">PELAPORAN KANAGATA RETAK<br>金型故障報告</span><br>
      <p style="font-size: 18px;">(Last Update: {{ date('d-M-Y H:i:s') }})</p>
      <p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
      <h3>(Request ID : {{$data['kanagata_request']->request_id}})</h3>
      <?php if ($data['kanagata_request']->status_shoot == 'Total Shoot Kanagata Kurang dari Target Total Shoot'): ?>
        <h3><span style=" background-color: #fcd068; font-weight: bold;"> Kesimpulan : {{$data['kanagata_request']->status_shoot}}</span><br><span style=" background-color: #fcd068;"><i>結論 : 金型の目標ショット数より実際ショット数が下回る</i></span></h3>
      <?php endif ?>
      <?php if ($data['kanagata_request']->status_shoot == 'Total Shoot Kanagata Melebihi Target Total Shoot'): ?>
        <h3><span style=" background-color: #fcd068; font-weight: bold;"> Kesimpulan : {{$data['kanagata_request']->status_shoot}}</span><span style=" background-color: #fcd068;"><br><i>結論 : 金型の目標ショット数より実際ショット数が上回る</i></span></h3>
      <?php endif ?>
      <?php if ($data['kanagata_request']->comment_users != null): ?>
        <h3><span style=" background-color: orange; font-weight: bold;"> Comment :
        </span><br> <?php echo $data['kanagata_request']->comment_users ?> </h3>
      <?php endif ?>
    </center>
  </div>      
  <div>
    <center>
      <div style="width: 100%">
        <center>
          <table style="width: 70%; font-family: arial; border-collapse: collapse; text-align: left;">
            <thead>
              <tr >
               <th colspan="9" style="border:1px solid black; font-size: 13px;  font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Kanagata Information (金型の情報)</th>
               <th colspan="3" style="font-size: 13px;"></th>
               <th colspan="6" style="border:1px solid black; font-size: 13px;  font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Repair Information (修理の情報)</th>
             </tr>
             <tr>
              <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Incident Date (発生日)</td>
              <td colspan="4" style="font-size: 13px; border: 1px solid black;">{{$data['kanagata_request']->tanggal_kejadian}}</td>

              <th colspan="3" style="font-size: 13px;"></th>

              <td colspan="3" style="font-size: 12px; border: 1px solid black;font-weight: bold">NG Pada Area Proses Sanding Normal (通常のサンディング加工エリア)</td>
              <td colspan="3" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->ng_sanding}}</td>
            </tr>
            <tr>
              <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold"> Problem Description (問題内容)</td>
              <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->problem_desc}}</td>

              <th colspan="3" style="font-size: 13px;"></th>
              <?php if ($data['kanagata_request']->ng_sanding == 'Tidak'): ?>

                <td colspan="3" style="font-size: 12px; border: 1px solid black;font-weight: bold">Bisa Repair (修正可)</td>
                <td colspan="3" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->repair}}</td>
              <?php endif ?>
              <?php if ($data['kanagata_request']->ng_sanding == 'Ya'): ?>
               <th colspan="3" style="font-size: 13px;"></th>
               <th colspan="3" style="font-size: 13px;"></th>
             <?php endif ?>

           </tr>

           <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Type Process (加工種別)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->process_type}}</td>
            <th colspan="3" style="font-size: 13px;"></th>
            <?php if ($data['kanagata_request']->ng_sanding == 'Tidak'): ?>

              <td colspan="3" style="font-size: 12px; border: 1px solid black;font-weight: bold">Time Repair (修正時間)</td>
              <td colspan="3" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->waktu_repair}} (detik)</td>
            <?php endif ?>
            <?php if ($data['kanagata_request']->ng_sanding == 'Ya'): ?>
             <th colspan="3" style="font-size: 13px;"></th>
             <th colspan="3" style="font-size: 13px;"></th>
           <?php endif ?>

         </tr>
         <tr>
           <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">GMC Material (GMC番号)</td>
           <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->gmc_material}}</td>

           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
         </tr>

         <tr>
           <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Description Material (部品名称)</td>
           <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->desc_material}}</td>

           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>

         </tr>
         <tr>
           <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Description Product (説明製品)</td>
           <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->part_name}}</td>
           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
         </tr>

         <tr>
           <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Type Die (金型種別)</td>
           <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->type_die}}</td>

           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>
           <th colspan="3" style="font-size: 13px;"></th>

         </tr>


         <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">No. Die (金型種別)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->no_die}}</td>

          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>

        </tr>
        <?php if ($data['kanagata_request']->process_type == 'Forging'): ?>
          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Molding Production Date (金型製作日)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black">{{$data['kanagata_request']->making_date}}</td>

            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
        <?php endif ?>

        <?php if ($data['kanagata_request']->status_shoot != ''): ?>

          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold; background-color: #fcd068;">Total Shoot (ショット数)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right; background-color: #fcd068;">{{$data['kanagata_request']->total_shoot}}</td>

            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold;background-color: #fcd068;">Target Total Shoot (目標ショット数)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;background-color: #fcd068;">{{$data['kanagata_request']->lifetime}}</td>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
        <?php endif ?>

        <?php if ($data['kanagata_request']->status_shoot == ''): ?>

          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold;">Total Shoot (ショット数)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->total_shoot}}</td>

            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold;">Target Total Shoot (目標ショット数)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">-</td>

            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
        <?php endif ?>

        <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Spare Die (予備金型数)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->spare_die}}</td>

          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>

        </tr>
        <?php if ($data['kanagata_request']->process_type == 'Forging'): ?>

          <tr>
            <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Forging Ke (鍛造回数)</td>
            <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->forging_ke}}</td>

            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>
            <th colspan="3" style="font-size: 13px;"></th>

          </tr>
        <?php endif ?>

        <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Die High (ダイハイト)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->die_high}}</td>

          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>

        </tr>
        <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Limit Preasure / Peak (リミット圧/ピーク圧)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->limit_preasure}} / {{$data['kanagata_request']->peak}}</td>

          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>

        </tr>
        <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Cavity (キャビティ番号)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->cavity}}</td>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
        </tr>
        <tr>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; font-weight: bold">Retak Ke (割れた回数)</td>
          <td colspan="4" style="font-size: 13px; border: 1px solid black;text-align:right;">{{$data['kanagata_request']->retak_ke}}</td>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
          <th colspan="3" style="font-size: 13px;"></th>
        </tr>

      </thead>
    </table>
  </center>

  <br>
  <br>

  <div style="width: 85%">
    <table style="border:1px solid black; border-collapse: collapse;">
          <!-- <thead style="background-color: rgb(126,86,134);color: white">
          </thead> -->
          <tbody align="center">
            <tr>
              <td colspan="{{count($data['approval_progress'])}}" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align:center;">Approval Proces (加工承認)</td>
            </tr>
            <tr>
              @foreach($data['approval_progress'] as $approval)
              <td style="border:1px solid black; font-size: 13px; width: 12%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
                <?php if ($approval->remark == 'Applicant'){
                  echo 'Submitted By <br>申請者';
                }else if($approval->remark == 'Manager'){
                  if ($approval->status == 'Rejected') {
                    echo 'Rejected <br>却下';
                  }else{
                    echo 'Approve By <br>承認者';
                  }
                }else{
                  echo 'Approve By <br>承認者';
                } ?>
              </td>
              @endforeach
            </tr>
            <tr>
              @foreach($data['approval_progress'] as $approval)
              <td style="border:1px solid black; font-size: 13px; width: 12%; height: 15; font-weight: bold;padding-top:35px;padding-bottom:35px;text-align:left;">
                <?php if ($approval->status == null){
                  echo '';
                }else{
                  echo $approval->status.'<br>'.date('d M Y',strtotime($approval->approved_at)).'<br>'.date('H:i:s',strtotime($approval->approved_at));
                } ?>
              </td>
              @endforeach
            </tr>
            <tr>
              @foreach($data['approval_progress'] as $approval)
              <td style="border:1px solid black; font-size: 13px; width: 12%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
                <?php if ($approval->approver_name != ''){
                  if (count(explode(' ', $approval->approver_name)) > 1) {
                    echo explode(' ', $approval->approver_name)[0].' '.explode(' ', $approval->approver_name)[1];
                  }else{
                    echo $approval->approver_name;
                  }
                }else{
                  echo '';
                } ?>
              </td>
              @endforeach
            </tr>
            <tr>
              @foreach($data['approval_progress'] as $approval)
              <td style="border:1px solid black; font-size: 13px; width: 12%; height: 15; font-weight: bold;background-color: #d4e157;text-align:left;">
                {{$approval->remark}}
              </td>
              @endforeach
            </tr>
          </tbody>
        </table>
        <br>
      </div>
    </center>
  </div>
</center>
<center>
  <br>
  <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i></span><br><span style=" background-color: orange;">&#8650; <i>(下をクリックしてください)</i></span><br><br>
  <?php if ($data['kanagata_request']->position != 'manager japanese speacialist pe'): ?>
    <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('approval/comment/'.$data['kanagata_request']->id.'/'.$data['remarks']) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/reject/'.$data['kanagata_request']->id.'/'.$data['remarks']) }}">&nbsp; Reject (却下) &nbsp;</a>
    <br><br>
  <?php endif ?>
  <?php if ($data['kanagata_request']->position == 'manager japanese speacialist pe'): ?>
    <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url('decision/kanagata/approval/'.$data['kanagata_request']->id.'/'.$data['remarks']) }}">&nbsp;&nbsp;&nbsp; Approve & Decision (承認と決定) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('approval/reject/'.$data['kanagata_request']->id.'/'.$data['remarks']) }}">&nbsp; Reject (却下) &nbsp;</a>
    <br><br>
  <?php endif ?>
</div>
<br>
<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
<a style="width: 50px;text-decoration: underline;" href="{{ url('detail/kanagata/'.$data['kanagata_request']->request_id) }}">Detail Infomation 金型詳細情報</a>

</center>


</body>
</html>