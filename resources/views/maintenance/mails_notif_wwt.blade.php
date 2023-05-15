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
      <span style="font-weight: bold; color: purple; font-size: 24px;">Notifikasi WWT</span><br><br>
    </center>
  </div>      
  <div>
    <center>
        <?php for ($i=0; $i < count($data['isi_mails']); $i++) { ?>
          <?php $b = explode(',', $data['isi_mails'][$i]->jenis) ?>
          <?php $c = explode(',', $data['isi_mails'][$i]->quantity) ?>
          <table style="border-collapse: collapse; width: 50%">
            <thead>
              <tr align="center">
                <th colspan="5" style="border:1px solid black; font-size: 15px; background-color: #f6d965; height: 20; text-align: center;">Limbah {{ $b[0] }}</th>
              </tr>
              <?php $no = 1 ?>
              <tr align="center"> 
                <td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">NO. LIMBAH</td>
                <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;">SLIP</td>
                <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">BERAT</td>
                <td style="border:1px solid black; font-size: 13px; width: 30%; height: 20;">JML. JUMBO BAG</td>
                <td style="border:1px solid black; font-size: 13px; width: 10%; height: 20;">MASA SIMPAN</td>
              </tr>
            </thead>
            <tbody id="bodyTableOutstanding">
              <?php for ($a=0; $a < count($b); $a++) { ?>
                <tr align="center"> 
                  <td style="border:1px solid black; font-size: 13px; height: 20;">{{ $no++ }}</td>
                  <td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['isi_mails'][$i]->slip }}</td>
                  <td style="border:1px solid black; font-size: 13px; height: 20;">{{ $c[$a] }} KG</td>
                  <td style="border:1px solid black; font-size: 13px; height: 20;">1</td>
                  <td style="border:1px solid black; font-size: 13px; height: 20;">{{ $data['isi_mails'][$i]->jml}} Hari</td>
                </tr>
              <?php } ?>
            </tbody>
          </table><br>
        <?php } ?>
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
</body>
</html>