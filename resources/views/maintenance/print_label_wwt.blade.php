<!DOCTYPE html>
<html>
<head>
  <title>WWT</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body>
    <table style="border: 5px solid black; width: 100%; background-color: yellow;">
      <tr>
        <th colspan="3" style="text-align: center;font-weight: bold; color: red; font-size: 30pt">
          PERINGATAN !
        </th>
      </tr>
      <tr>
        <th colspan="3" style="text-align: center; font-weight: normal; color: black; font-size: 20pt">
          LIMBAH BAHAN BERBAHAYA DAN BERACUN
        </th>
      </tr>
    </table>
    <table style="border: 5px solid black; width: 100%; background-color: yellow">
      <tr>
        <th style="text-align: left;padding-top: 20px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          PENGHASIL
        </th>
        <th style="text-align: center;padding-top: 20px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th colspan="4" style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          PT. Yamaha Musical Products Indonesia
        </th>
      </tr>

      <tr>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          ALAMAT
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th colspan="3" style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          JL. Rembang Industri I/36 PIER
          <br>
          Pasuruan - 67152
          <br>
          TELP : (0343) 740290 - FAX : (0343) 740291
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          @if($data[0]->waste_category == 'Lubricant Oil' || $data[0]->waste_category == 'Painting Liquid Laste' || $data[0]->waste_category == 'Liquid Cleaning Waste')
          <img src="{{ public_path() . '/data_file/wwt/label/mudah_menyala.jpg' }}" width="90" height="90" class="cropped">
          @else
          <img src="{{ public_path() . '/data_file/wwt/label/beracun.jpg' }}" width="90" height="90" class="cropped">
          @endif
        </th>
      </tr>

      <tr>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          NOMOR PENGHASIL
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          -
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          NOMOR LIMBAH
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          <img src="{{ public_path() . '/data_file/wwt/qr_code/'.$data[0]->slip.'.png' }}" width="90" height="90" class="cropped">
        </th>
      </tr>

      <tr>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          TGL. PENGEMASAN
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          {{ $data[0]->date_in }}
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          KODE LIMBAH
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          {{ $data[0]->kode_limbah }}
        </th>
      </tr>

      <tr>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          JENIS LIMBAH
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          {{ $data[0]->waste_category }}
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          SIFAT LIMBAH
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt">
          {{ $sifat_limbah }}
        </th>
      </tr>

      <tr>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
          JUMLAH LIMBAH
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
          :
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
          {{ $data[0]->quantity }} KG
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
        </th>
        <th style="text-align: center;padding-top: 5px;font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
        </th>
        <th style="text-align: left;padding-top: 5px; padding-left:  20px; font-weight: normal; color: black; font-size: 11pt; padding-bottom: 20px">
        </th>
      </tr>
    </table>
</body>
</html>