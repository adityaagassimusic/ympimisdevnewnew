<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
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
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="center">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">Data Calon Karyawan</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">A.  Data Pribadi </td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">NIK</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->nik }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Nama Lengkap</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->name }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Tempat / Tanggal Lahir</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->birth_place }} / <?php echo date("d-m-Y",strtotime($isi->birth_date));?></td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Agama</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->religion }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Jenis Kelamin</td>
                @if($isi->gender == 'Male')
                <td style="border:1px solid black; font-size: 12px;">LAKI - LAKI</td>
                @elseif($isi->gender == 'Female')
                <td style="border:1px solid black; font-size: 12px;">PEREMPUAN</td>
                @endif
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Alamat Tinggal Saat Ini</td>
                <?php
                  $address_current = explode("/", $isi->current_address);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $address_current[0] }} RT {{ $address_current[1] }} RW {{ $address_current[2] }} KELURAHAN {{ $address_current[3] }} KECAMATAN {{ $address_current[4] }} KOTA/KAB {{ $address_current[5] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Alamat Asal</td>
                <?php
                  $address = explode("/", $isi->address);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $address[0] }} RT {{ $address[1] }} RW {{ $address[2] }} KELURAHAN {{ $address[3] }} KECAMATAN {{ $address[4] }} KOTA/KAB {{ $address[5] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Hand Phone</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->handphone }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">Email</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->email }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">NPWP</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->npwp }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">BPJS Ketenagakerjaan</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->bpjstk }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 30%">BPJS Kesehatan</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $isi->bpjskes }}</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">B. Susunan Keluarga (Ayah, Ibu dan Saudara Sekandung termasuk anak sendiri)</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center"></td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Nama</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">L/P</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tempat Lahir</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tanggal Lahir</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Pekerjaan</td>
              </tr>
              <tr>
                <?php
                  $ayah = explode("_", $isi->f_ayah);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Ayah</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ayah[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ayah[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ayah[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ayah[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ayah[4] }}</td>
              </tr>
              <tr>
                <?php
                  $ibu = explode("_", $isi->f_ibu);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Ibu</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ibu[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ibu[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ibu[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ibu[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $ibu[4] }}</td>
              </tr>
              <tr>
                <?php
                  $saudara1 = explode("_", $isi->f_saudara1);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Saudara 1</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara1[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara1[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara1[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara1[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara1[4] }}</td>
              </tr>
              <tr>
                <?php
                  $saudara2 = explode("_", $isi->f_saudara2);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Saudara 2</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara2[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara2[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara2[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara2[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara2[4] }}</td>
              </tr>
              <tr>
                <?php
                  $saudara3 = explode("_", $isi->f_saudara3);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Saudara 3</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara3[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara3[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara3[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara3[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara3[4] }}</td>
              </tr>
              <tr>
                <?php
                  $saudara4 = explode("_", $isi->f_saudara4);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Saudara 4</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara4[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara4[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara4[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara4[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $saudara4[4] }}</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">C. Susunan Keluarga (Suami / Istri dan Anak-anak) </td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center"></td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Nama</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">L/P</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tempat Lahir</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tanggal Lahir</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Pekerjaan</td>
              </tr>
              <tr>
                <?php
                  $pasangan = explode("_", $isi->m_pasangan);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Suami / Isteri</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $pasangan[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $pasangan[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $pasangan[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $pasangan[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $pasangan[4] }}</td>
              </tr>
              <tr>
                <?php
                  $anak1 = explode("_", $isi->m_anak1);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Anak 1</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak1[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak1[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak1[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak1[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak1[4] }}</td>
              </tr>
              <tr>
                <?php
                  $anak2 = explode("_", $isi->m_anak2);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Anak 2</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak2[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak2[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak2[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak2[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak2[4] }}</td>
              </tr>
              <tr>
                <?php
                  $anak3 = explode("_", $isi->m_anak3);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Anak 3</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak3[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak3[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak3[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak3[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak3[4] }}</td>
              </tr>
              <tr>
                <?php
                  $anak4 = explode("_", $isi->m_anak4);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Anak 4</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak4[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak4[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak4[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak4[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak4[4] }}</td>
              </tr>
              <tr>
                <?php
                  $anak5 = explode("_", $isi->m_anak5);
                ?>
                <td style="border:1px solid black; font-size: 12px;">Anak 5</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak5[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak5[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak5[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak5[3] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $anak5[4] }}</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">D. Pendidikan Formal</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center"></td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Nama Lembaga Pendidikan</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Jurusan</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tahun Masuk</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Tahun Lulus</td>
              </tr>
              <tr>
                <?php
                  $sd = explode("_", $isi->sd);
                ?>
                <td style="border:1px solid black; font-size: 12px;">SD</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sd[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sd[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sd[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sd[3] }}</td>
              </tr>
              <tr>
                <?php
                  $smp = explode("_", $isi->smp);
                ?>
                <td style="border:1px solid black; font-size: 12px;">SMP</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $smp[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $smp[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $smp[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $smp[3] }}</td>
              </tr>
              <tr>
                <?php
                  $sma = explode("_", $isi->sma);
                ?>
                <td style="border:1px solid black; font-size: 12px;">SMA</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sma[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sma[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sma[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $sma[3] }}</td>
              </tr>
              <tr>
                <?php
                  $s1 = explode("_", $isi->s1);
                ?>
                <td style="border:1px solid black; font-size: 12px;">S1</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s1[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s1[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s1[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s1[3] }}</td>
              </tr>
              <tr>
                <?php
                  $s2 = explode("_", $isi->s2);
                ?>
                <td style="border:1px solid black; font-size: 12px;">S2</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s2[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s2[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s2[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s2[3] }}</td>
              </tr>
              <tr>
                <?php
                  $s3 = explode("_", $isi->s3);
                ?>
                <td style="border:1px solid black; font-size: 12px;">S3</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s3[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s3[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s3[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $s3[3] }}</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">E. Kondisi Darurat Yang Bisa Dihubungi</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Nama</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">No Telepon</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Pekerjaan</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Hubungan</td>
              </tr>
              <tr>
               <?php
                  $emergency1 = explode("_", $isi->emergency1);
               ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency1[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency1[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency1[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency1[3] }}</td>
              </tr>
              <tr>
                <?php
                  $emergency2 = explode("_", $isi->emergency2);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency2[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency2[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency2[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency2[3] }}</td>
              </tr>
              <tr>
                <?php
                  $emergency3 = explode("_", $isi->emergency3);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency3[0] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency3[1] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency3[2] }}</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $emergency3[3] }}</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody align="left">
              <tr>
                <td colspan="2" style="border:1px solid black; font-size: 14; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">F. Lain - Lain</td>
              </tr>
      </tbody>            
      </table><br>
      <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
      <tbody>
              <tr>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25; width: 40%" align="center">Pertanyaan</td>
                <td style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25" align="center">Jawaban</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">1. Bersediakah anda bekerja lembur bila diperlukan ? Jelaskan alasannya!</td>
                <?php
                  $answer1 = explode("/", $answer->answer1);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer1[0] }} - {{ $answer1[1] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">2. Besediakah anda dipindah bagian bila diperlukan ? Jelaskan alasannya!</td>
                <?php
                  $answer2 = explode("/", $answer->answer2);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer2[0] }} - {{ $answer2[1] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">3. Bersediakah anda dipindah ke daerah lain, dinas luar kota bila diperlukan ? Jelaskan alasanya.</td>
                <?php
                  $answer3 = explode("/", $answer->answer3);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer3[0] }} - {{ $answer3[1] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">4. Sebutkan secara berurut, apa yang anda prioritaskan : Gaji, Suasana Kerja, Kedudukan.</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer4 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">5.Jenis pekerjaan apa yang sebenarnya anda sukai : Kantor, Lapangan, Dinas Luar, dsb Mengapa ?</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer5 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">6. Apakah anda memiliki hubungan keluarga / teman baik dengan seseorang (karyawan) Perusahaan ini ? Sebutkan dan jelaskan!</td>
                <?php
                  $answer6 = explode("/", $answer->answer6);
                ?>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer6[0] }} - {{ $answer6[1] }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">7. Sebutkan nama, alamat, No. Telp, orang yang dapat kami hubungi untuk memperoleh referensi tentang diri anda ?</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer7 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">8. SIM apa yang anda miliki.</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer8 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">9. Jika anda dinyatakan bisa bergabung dengan Perusahaan ini, kapan anda bersedia mulai bekerja ?</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer9 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">10.Apa  yang anda harapkan bilamana anda bergabung dengan Perusahaan ini ? Mengapa?</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer10 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">11. Jika anda diterima di PT. Yamaha Musical Products Indonesia, berapa gaji yang anda harapkan ?</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer11 }}</td>
              </tr>
              <tr>
                <td style="border:1px solid black; font-size: 12px;">12. Tuliskan nomor Ijazah Pendidikan terakhir Anda.</td>
                <td style="border:1px solid black; font-size: 12px;">{{ $answer->answer12 }}</td>
              </tr>
      </tbody>            
      </table>
  </div>
  </center>
</div>
</body>
</html>