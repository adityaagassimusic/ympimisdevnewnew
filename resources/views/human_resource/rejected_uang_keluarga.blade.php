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
      <span style="font-weight: bold; color: purple; font-size: 24px;">PERMOHONAN TUNJANGAN KELUARGA</span><br>
      <span style="font-weight: bold; font-size: 26px;">{{ $data['data'][0]->request_id }}</span>
    </center>
  </div>      
  <div>
    <center>
      <div style="width: 80%">
        <span style="font-weight: bold; font-size: 15px; text-align: left;">Yang bertanda tangan di bawah ini, saya : </span><br>
        <table style="border:1px solid black; border-collapse: collapse;">
          <tbody>
            <tr align="center">
              <td colspan="2" style="border:1px solid black; font-size: 15px; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">Details</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 10%; height: 20; font-weight: bold;">
                NIK
              </td>
              <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">
                {{ $data['data'][0]->employee }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Nama
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->name }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Department
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->department }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Section
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->seksi }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Group
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->group }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Sub Group
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->sub_group }}
              </td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; height: 20; font-weight: bold;">
                Posisi
              </td>
              <td style="border:1px solid black; font-size: 13px; height: 20;">
                {{ $data['data'][0]->jabatan }}
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <span style="font-weight: bold; font-size: 15px; text-align: left">Dengan ini mengajukan permohonan untuk mendapatkan tunjangan keluarga, yaitu : </span><br><br>
        <table style="border:1px solid black; border-collapse: collapse; width: 60%;">
          <tbody align="center">
            <tr>
              <td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white;text-align: center;">{{ $data['data'][0]->permohonan }}</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table style="border:1px solid black; border-collapse: collapse; width: 60%;">
          <tbody align="center">
            <tr>
              <td colspan="2" style="border:1px solid black; font-size: 15px; width: 20%; height: 20; font-weight: bold; background-color: #dd4b39;color: white;text-align: center;">REJECTED</td>
            </tr>
          </tbody>
        </table>
        <br>
        <br>
        <table style="border: 1px solid black; border-collapse: collapse; width: 60%;" align="center">
          <thead align="center">
            <tr>
              <?php
              for ($i=0; $i < count($data['approver_progress']); $i++) {
                print_r('<th style="border: 1px solid black; width: 10%; text-align: center">'.$data['approver_progress'][$i]->remark.'</th>');
              }?>
            </tr>
            <tr style="height: 15px">
              <?php
              for ($i=0; $i < count($data['approver_progress']); $i++) {
                print_r('<td style="border: 1px solid black; width: 1%; height: 50px; text-align: center">'.$data['approver_progress'][$i]->status.'<br>'.$data['approver_progress'][$i]->approved_at.'</td>');
              }?>
            </tr>
            <tr>
              <?php
              for ($i=0; $i < count($data['approver_progress']); $i++) {
                print_r('<th style="border: 1px solid black; width: 10%; text-align: center">'.$data['approver_progress'][$i]->approver_name.'</th>');
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