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
      font-size: 16px;
      margin: 4px 2px;
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
      <p>This is an automatic notification. Please do not reply to this address. 自動メールです。返信しないでください。</p>
      <span style="font-weight: bold; color: purple; font-size: 24px;">MIRAI REQUEST MANPOWER (MIRAI 承認システム)</span><br>
      <span style="font-weight: bold; font-size: 26px;">{{ $data['data'][0]->request_id }}</span>
    </center>
  </div>      
  <div>
    <center>
      <div style="width: 80%">
        <table style="border:1px solid black; border-collapse: collapse;">
          <tbody align="center">
            <tr>
              <td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Details</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                NIK - Name Aplicant
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                {{ $data['data'][0]->employee_id }} - {{ $data['data'][0]->name }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Department
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                {{ $data['data'][0]->department }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Posisi
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                {{ $data['data'][0]->position }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Status
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                {{ $data['data'][0]->employment_status }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Jumlah
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                 L ({{ $data['data'][0]->quantity_male }}) / P ({{ $data['data'][0]->quantity_female }})
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Alasan Penambahan
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                 {{ $data['data'][0]->reason}}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Perkiraan Tanggal Masuk
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                 {{ $data['data'][0]->start_date}}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Jenis Request Manpower
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                 {{ $data['data'][0]->start_date}}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                Status Request
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">
                 {{ $data['data'][0]->status_req}}
              </td>
            </tr>
          </tbody>
        </table>
        <?php if (ISSET($data['remark']) && $data['remark'] == 'Recruitment HR'){ ?>

        <?php }else if(ISSET($data['posisi']) && $data['posisi'] == 'Rejected'){ ?>
          <table style="width: 100%">
            <tr>
                <a style="background-color: #ff6090; width: 50px;text-decoration: none;color: black;font-size: 20px;">&nbsp;&nbsp;&nbsp; Reject (却下) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;
            </tr>
          </table>
          <table style="width: 100%">
            <tr>
                {{ $data['data'][0]->reason_reject}}
            </tr>
          </table>
        <?php }else if(ISSET($data['posisi']) && $data['posisi'] == 'user'){ ?>
          <table style="width: 100%">
            <tr>
              <th>
                <h2>Jawab Pertanyaan Approver :</h2><br>
                <a style="background-color: #ccff90; width: 50px;text-decoration: none;color: black;font-size: 20px;" href="{{ url('human_resource/comment/reply/').'/'.$data['data'][0]->request_id }}">&nbsp;&nbsp;&nbsp; Reply (保留してコメント入力) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;
              </th>
            </tr>
          </table>
        <?php }else{ ?>
          <br>
          <table style="width: 100%">
            <tr>
                <a style="background-color: #ff6090; width: 50px;text-decoration: none;color: black;font-size:20px;" href="{{ url('human_resource/rejected/').'/'.$data['data'][0]->request_id }}">&nbsp;&nbsp;&nbsp; Reject (却下) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                <a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url('human_resource/comment/').'/'.$data['data'][0]->request_id }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留してコメント入力) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;

                <a style="background-color: #ccff90; width: 50px;text-decoration: none;color: black;font-size: 20px;"  href="{{ url('human_resource/appproval/confirm/').'/'.$data['data'][0]->request_id.'/'.$data['app_email']->id }}">&nbsp; Approve (承認) &nbsp;</a>
            </tr>
          </table>
        <?php } ?>
        <br>
        <br>
        <table style="border: 1px solid black; border-collapse: collapse; width: 100%;" align="center">
          <thead align="center">
            <tr>
              <?php
              for ($i=0; $i < count($data['app_progress']); $i++) {
                print_r('<th style="border: 1px solid black; width: 10%;">'.$data['app_progress'][$i]->remark.'</th>');
              }?>
            </tr>
            <tr style="height: 15px">
              <?php
              for ($i=0; $i < count($data['app_progress']); $i++) {
                  print_r('<td style="border: 1px solid black; width: 1%; height: 50px;">'.$data['app_progress'][$i]->status.'<br>'.$data['app_progress'][$i]->approved_at.'</td>');
              }?>
            </tr>
            <tr>
              <?php
              for ($i=0; $i < count($data['app_progress']); $i++) {
                print_r('<th style="border: 1px solid black; width: 10%;">'.$data['app_progress'][$i]->approver_name.'</th>');
              }
              ?>
            </tr>
          </thead>
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
      </div>
    </center>
  </div>
</body>
</html>