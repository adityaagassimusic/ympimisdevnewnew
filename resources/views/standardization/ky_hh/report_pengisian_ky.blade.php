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
</style>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div>
    <center>

      <div style="width: 100%">
        <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
          <thead>
            <tr>
              <td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
            </tr>
            <tr>
              <td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
            </tr>
            <tr>
              <td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
            </tr>
            <tr>
              <td colspan="10" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
            </tr>
          </thead>
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody align="center">
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; width: 25%; height: 30; background-color:  #e8daef ">Tanggal Pelaksanaan</td>
              <td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; width: 25%">{{ $data[0]->tanggal }}</td>
              <td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color: #e8daef; width: 25%">Nama Bagian</td>
              <td colspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color: #e8daef; width: 25%">TTD</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30;">{{ $data[0]->nama_tim }} - {{ $data[0]->department_short }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 60;">TTD 1</td>
              <td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 60;">TTD 2</td>
            </tr>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">Nama Pekerjaan/Kejadian</td>
              <td rowspan="2" colspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; height: 30;">{{ $data[0]->kode_soal }} - {{ $data[0]->remark }}</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">{{ $data[0]->ketua }}</td>
              <td style="border:1px solid black; font-size: 10; font-weight: bold; height: 30; background-color:  #e8daef ">{{ $data[0]->wakil }}</td>
            </tr>
          </tbody>            
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 1</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%">Menentukan jenis potensi bahaya yang akan terjadi sesuai dengan kasus yang ada</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 2</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%">Memprediksikan faktor bahaya (tindakan tidak aman+kondisi tidak aman) dan gejala yang bisa timbul. Serta memilih item yang paling dianggap berbahaya dari bahaya yang ditemukan dan lingkarilah.</td>
            </tr>
          </tbody>            
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">No</td>
              <td style="border:1px solid black; font-size: 12px; width: 35%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Faktor Bahaya (Tindakan yang tidak aman+kondisi yang tidak aman)</td>
              <td style="border:1px solid black; font-size: 12px; width: 20%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Jenis Kecelakaan</td>
              <td style="border:1px solid black; font-size: 12px; width: 15%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;"></td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Kesimpulan</td>
            </tr>
            <?php
              $faktor_bahaya = explode('/', $data[0]->faktor_bahaya);
              $faktor_benda = explode('/', $data[0]->faktor_benda);
              $jenis_kecelakaan = explode('/', $data[0]->jenis_kecelakaan);
              $kesimpulan = explode('/', $data[0]->kesimpulan);
              $konkrit = explode('/', $data[0]->konkrit);
            ?>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">1.</td>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[0] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $jenis_kecelakaan[0] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;"></td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $kesimpulan[0] }}</td>
            </tr>
            <tr>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[0] }}</td>
            </tr>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">2.</td>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[1] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $jenis_kecelakaan[1] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;"></td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $kesimpulan[1] }}</td>
            </tr>
            <tr>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[1] }}</td>
            </tr>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">3.</td>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[2] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $jenis_kecelakaan[2] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;"></td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $kesimpulan[2] }}</td>
            </tr>
            <tr>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[2] }}</td>
            </tr>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">4.</td>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[3] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $jenis_kecelakaan[3] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;"></td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $kesimpulan[3] }}</td>
            </tr>
            <tr>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[3] }}</td>
            </tr>
            <tr>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">5.</td>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Orang)<br><br>{{ $faktor_bahaya[4] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $jenis_kecelakaan[4] }}</td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;"></td>
              <td rowspan="2" style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">{{ $kesimpulan[4] }}</td>
            </tr>
            <tr>
              <td style="padding-top: 10px; border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center">(Tidak Aman Benda)<br><br>{{ $faktor_benda[4] }}</td>
            </tr>
          </tbody>            
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 3</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%">(Menyusun Tindakan penanggulangan/apa yang akan anda lakukan). Memikirkan penanggulangan yang bisa dilakukan secara konkret untuk menyelesaikan item-item. [Point bahaya yang telah dilingkari].</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25;">Tahap Ke 4</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%">Menetapkan [Item pelaksanaan penting]. Dan diberi tanda # kemudian menjadikannya target tindakan Tim untuk merealisasikannya.</td>
            </tr>
          </tbody>            
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">No</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Penanggulangan Konkret</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">#</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">1.</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[0] }}</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
            </tr>
             <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">2.</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[1] }}</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
            </tr>
             <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">3.</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[2] }}</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
            </tr>
             <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">4.</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[3] }}</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
            </tr>
             <tr>
              <td style="border:1px solid black; font-size: 12px; width: 5%; height: 25; text-align: center;">5.</td>
              <td style="border:1px solid black; font-size: 12px; width: 70%; height: 25; text-align: center;">{{ $konkrit[4] }}</td>
              <td style="border:1px solid black; font-size: 12px; width: 25%; height: 25; text-align: center;">-</td>
            </tr>
          </tbody>            
        </table>
        <br>

        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Target / Tindakan Tim</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%; font-weight: bold; height: 25; text-align: center;">{{ $data[0]->target_tindakan }}</td>
            </tr>
            <tr>
              <td style="border:1px solid black; font-size: 12px; width: 25%; font-weight: bold; background-color:  #e8daef ; height: 25; text-align: center;">Item Ikrar (Yubishasi Koshou)</td>
              <td style="border:1px solid black; font-size: 12px; width: 75%; font-weight: bold; height: 25; text-align: center;">{{ $data[0]->ikrar }}</td>
            </tr>
          </tbody>            
        </table>
        <br>
        <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;">
          <thead>
            <tr>
              <td colspan="10" style="font-weight: bold;font-size: 10px">---------- <?php
              echo "Update : " . date("Y-m-d h:i");
            ?> ----------</td>
          </tr>
        </table>
      </div>
    </center>
  </div>
</body>
</html>