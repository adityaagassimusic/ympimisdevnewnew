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
      font-size: 9pt;
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
        src: url("{{ storage_path('data_file\arial.ttf') }}");
        font-style: normal;
    }

    .ttd {
        font-family: arial;
      }

    @page { margin: 30px 30px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

  </style>
</head>
<body style="">
  <div style="width: 100%;">
    <table style="width: 100%;">
      <tr>
        <th style="text-align: left;padding-top: 0px"><img width="100px" src="{{ url('/data_file/yamaha.png') }}"></th>
      </tr>
       <tr>
        <th style="text-align: center;padding-top: 0px;font-weight: bold;font-size: 14pt">
          HASIL PEMERIKSAAN FISIK KARYAWAN
        </th>
      </tr>
      <tr>
        <td>
          <table style="margin-top: 20px;">
            <tr>
              <td>NAMA</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{strtoupper($physical->name)}}</td>
              <td style="padding-left: 20px;">TANGGAL PEMERIKSAAN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{strtoupper($physical->dates)}}</td>
            </tr>
            <tr>
              <td>NIK</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>
                {{strtoupper($physical->employee_id)}}
              </td>
              <td style="padding-left: 20px;">UMUR</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->age}}</td>
            </tr>
            <tr>
              <td>BAGIAN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>
                <?php $dept = ''; ?>
                <?php for ($i=0; $i < count($emp); $i++) { 
                if ($emp[$i]->employee_id == $physical->employee_id) {
                  $dept = $emp[$i]->department;
                }
              } ?> 
              <?php echo strtoupper($dept) ?>
              </td>
              <td style="padding-left: 20px;">DOKTER PEMERIKSA</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>DR. TALIFFIA SETYA H.</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td style="font-weight: bold;padding-top: 20px;">
          RIWAYAT PENYAKIT
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <?php $disease = [
              'EPILEPSI',
              'ASMA',
              'DIABETES MELLITUS',
              'SAKIT JANTUNG',
              'TBC',
              'KEGANASAN',
              'HEPATITIS',
              'OPERASI',
              'ALERGI',
            ]; ?>
            <?php for ($i=0; $i < count($disease); $i++) { ?>
              <tr>
                <td>{{$disease[$i]}}</td>
                <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
                <?php $dis = 'Tidak'; ?>
                <?php if ($physical->disease_history != 'Tidak Ada') {
                  $diseases = explode('<br>', $physical->disease_history);
                  for ($j=0; $j < count($diseases); $j++) { 
                    if ($diseases[$j] == $disease[$i]) {
                      $dis = 'Ya';
                    }
                  }
                } ?>
                <td>{{$dis}}</td>
              </tr>
            <?php } ?>
          </table>
        </td>
      </tr>

      <tr>
        <td style="font-weight: bold;padding-top: 20px;">
          PEMERIKSAAN FISIK
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td>TEKANAN DARAH</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->blood_pressure}} MMHG</td>
            </tr>
            <tr>
              <td>NADI</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->pulse}} X/MNT</td>
            </tr>
            <tr>
              <td>RESPIRASI</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->respiration}} X/MNT</td>
            </tr>
            <tr>
              <td>TINGGI BADAN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->height}} CM</td>
            </tr>
            <tr>
              <td>BERAT BADAN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->weight}} KG</td>
            </tr>
            <tr>
              <td>IMT</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->imt}} KG/M</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="font-weight: bold;padding-top: 20px;">
          MATA
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td>FISIK MATA</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->visus_od_status}} - {{$physical->visus_os_status}}</td>
            </tr>
            <tr>
              <td>VISUS OD</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->visus_od}}</td>
            </tr>
            <tr>
              <td>VISUS OS</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->visus_os}}</td>
            </tr>
            <tr>
              <td>BUTA WARNA</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->color_blind}}</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td style="padding-top: 20px;">
          <table>
            <tr>
              <td>ASIMETRI WAJAH</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->symmetry}}</td>
            </tr>
            <tr>
              <td>THT</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->tht}}</td>
            </tr>
            <tr>
              <td>GIGI / MULUT</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->tooth}}</td>
            </tr>
            <tr>
              <td>KEPALA LEHER</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->head}}</td>
            </tr>
            <tr>
              <td>JANTUNG</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->heart}}</td>
            </tr>
            <tr>
              <td>PARU-PARU</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->lungs}}</td>
            </tr>
            <tr>
              <td>ABDOMEN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->abdomen}}</td>
            </tr>
            <tr>
              <td>HEPAR</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->hepar}}</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="font-weight: bold;padding-top: 20px;">
          PENYAKIT KULIT
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <?php $skin = [
              'DERMATITIS',
              'BERCAK MATI RASA',
              'KULIT MENGELUPAS',
            ]; ?>
            <?php for ($i=0; $i < count($skin); $i++) { ?>
              <tr>
                <td>{{$skin[$i]}}</td>
                <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
                <?php $skinss = 'Tidak'; ?>
                <?php if ($physical->skin != 'NORMAL') {
                  $skins = explode('<br>', $physical->skin);
                  for ($j=0; $j < count($skins); $j++) { 
                    if ($skins[$j] == $skin[$i]) {
                      $skinss = 'Ya';
                    }
                  }
                } ?>
                <td>{{$skinss}}</td>
              </tr>
            <?php } ?>
          </table>
        </td>
      </tr>

      <tr>
        <td style="font-weight: bold;padding-top: 20px;">
          EKSTEMITAS
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td>LENGAN</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->limbs}}</td>
            </tr>
            <tr>
              <td>TUNGKAI</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->arm}}</td>
            </tr>
            <tr>
              <td>RUANG GERAK SENDI</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->joint}}</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td style="font-weight: bold;">RIWAYAT FOTO THORAX TERAKHIR</td>
              <td>&nbsp;&nbsp;&nbsp;:&nbsp;</td>
              <td>{{$physical->thorax}}</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td style="align-self: right;align-content: right;align-items: right;padding-top: 15px;">
          <table style="width: 100%;text-align: right">
            <tr>
              <td style="font-weight: bold;">DOKTER PEMERIKSA</td>
            </tr>
            <tr>
              <td><img width="100px" src="{{ url('/data_file/ga/physical/dr_via.png') }}"></td>
            </tr>
            <tr>
              <td style="text-decoration: underline;font-weight: bold;">DR. TALIFFIA SETYA H.</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>