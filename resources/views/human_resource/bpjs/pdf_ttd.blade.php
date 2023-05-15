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
  
  <div style="width: 100%; padding-top: 90px">
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
        <thead>
          <tr>
            <td colspan="10" style="font-weight: bold;font-size: 22px">SURAT PERNYATAAN PENAMBAHAN ANGGOTA KELUARGA</td>
          </tr>
        </thead>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="10" style="text-align: left;font-size: 15px">Yang bertandatangan dibawah ini :</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Nama PIC / HRD</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">UMMI ERNAWATI</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Nomor KTP PIC / HRD</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">3514135907850002</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Jabatan PIC / HRD</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">Staff</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Nama Perusahaan</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">YAMAHA MUSICAL PRODUCTS INDONESIA PT.</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Kode BU (entitas)</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">02140017</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Nomor HP PIC / HRD</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">081249560805</td>
          </tr>
        </thead>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="10" style="text-align: left;font-size: 15px">Bersedia melakukan pemotongan iuran atas nama :</td>
          </tr>
        </thead>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead style="background-color: #BDD5EA; color: black;">
          <tr>
            <th width="10%" style="border:1px solid black; text-align: center; font-size: 15px">No</th>
            <th width="40%" style="border:1px solid black; text-align: center; font-size: 15px">Nama</th>
            <th width="50%" style="border:1px solid black; text-align: center; font-size: 15px">Hubungan Keluarga</th>
          </tr>
        </thead>
        <tbody>
          <?php
          for ($i=0; $i < count($resume); $i++) { 
            $no = $i+1;
            print_r('<tr><td style="border:1px solid black; font-size: 15px; width: 5%; height: 25; text-align: center;">'.$no++.'</td><td style="border:1px solid black; font-size: 15px; width: 5%; height: 25; text-align: center;">'.$resume[$i]->name.'</td><td style="border:1px solid black; font-size: 15px; width: 5%; height: 25; text-align: center;">'.$resume[$i]->hubungan.'</td></tr>');
          }
          ?> 
        </tbody>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="10" style="text-align: left;font-size: 15px">Dari karyawan kami :</td>
          </tr>
        </thead>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">Nama</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">{{ $judul->name }}</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: left;font-size: 15px">No Pegawai</td>
            <td style="text-align: left;font-size: 15px">:</td>
            <td colspan="5" style="text-align: left;font-size: 15px">{{ $judul->employee_id }}</td>
          </tr>
        </thead>
      </table>
      <br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="10" style="text-align: left;font-size: 15px">Sebesar <?php print_r(count($resume))?>% setiap bulannya terhitung mulai bulan April 2023, Sebagai bahan pertimbangan kami lampirkan juga Kartu Keluarga kami tersebut dan form kolom 37. Demikian surat pernyataan ini kami buat untuk digunakan sebagaimana mestinya.</td>
          </tr>
        </thead>
      </table>
      <br><br><br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="5" style="text-align: left;font-size: 15px">Pasuruan, {{ $date }}</td>
            <td colspan="5" style="text-align: left;font-size: 15px"></td>
          </tr>
          <tr>
            <td colspan="5" style="text-align: left;font-size: 15px">Yang Mengajukan (Peserta)</td>
            <td colspan="5" style="text-align: right;font-size: 15px">Yang Membuat Pernyataan</td>
          </tr>
        </thead>
      </table>
      <br><br><br><br><br><br>
      <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: center;" >
        <thead>
          <tr>
            <td colspan="5" style="text-align: left;font-size: 15px">{{ $judul->name }}</td>
            <td colspan="5" style="text-align: right;font-size: 15px">UMMI ERNAWATI</td>
          </tr>
        </thead>
      </table>
  </div>
  </center>
</div>
</body>
</html>