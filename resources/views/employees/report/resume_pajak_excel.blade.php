<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        table{
            border: 2px solid black;
            vertical-align: middle;
        }
        table > thead > tr > th{
            border: 2px solid black;
            height: 30;
            vertical-align: middle;
        }
        table > tbody > tr > td{
            border: 1px solid rgb(211,211,211);
            vertical-align: middle;
        }
        table > tfoot > tr > th{
            border: 1px solid rgb(211,211,211);
        }
    </style>
</head>
<body>
    @if(isset($npwp_detail) && count($npwp_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Timestamp</th>
                <th>Nomor Induk Karyawan</th>
                <th>Nama lengkap</th>
                <th>Status</th>
                <th>Nomor Induk Kependudukan</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat (Nama Jalan / Desa)</th>
                <th>RT / RW</th>
                <th>Kelurahan</th>
                <th>Kecamatan</th>
                <th>Kabupaten / Kota</th>
                <th>Status Pernikahan</th>
                <th>Tanggal Pernikahan</th>
                <th>Nama Istri</th>
                <th>Tanggal Lahir Istri</th>
                <th>Pekerjaan Istri</th>
                <th>Nama Anak 1</th>
                <th>Tempat Lahir Anak 1</th>
                <th>Tanggal Lahir Anak 1</th>
                <th>Status Anak 1</th>
                <th>Nama Anak 2</th>
                <th>Tempat Lahir Anak 2</th>
                <th>Tanggal Lahir Anak 2</th>
                <th>Status Anak 2</th>
                <th>Nama Anak 3</th>
                <th>Tempat Lahir Anak 3</th>
                <th>Tanggal Lahir Anak 3</th>
                <th>Status Anak 3</th>
                <th>Apakah sudah memiliki NPWP?</th>
                <th>Atas Nama Sendiri / ikut Suami?</th>
                <th>Nama Sesuai NPWP Sendiri / Suami</th>
                <th>Nomor NPWP</th>
                <th>Alamat Sesuai NPWP</th>  
                <th>Status Konfirmasi</th>
                <th>Status Ganti Data NPWP</th>
                <th>Status Ganti Nama NPWP</th>
                <th>Status Ganti Nomor NPWP</th>
                <th>Status Ganti Alamat NPWP</th>
                <th>Link Foto NPWP</th>
                <!-- <th>Foto NPWP</th> -->
            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
            ?>

            @foreach($npwp_detail as $npwp)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $npwp->updated_at }}</td>
                <td>{{ $npwp->employee_id }}</td>
                <td>{{ $npwp->nama }}</td>
                <td>
                    <?php

                        $jumlah = 0;

                        if($npwp->anak1 != "____"){
                          $jumlah++;
                        }
                        if($npwp->anak2 != "____"){
                           $jumlah++; 
                        }
                        if($npwp->anak3 != "____"){
                           $jumlah++; 
                        }

                    ?>

                    @if($npwp->status_perkawinan == "TIDAK KAWIN" || $npwp->npwp_status == "IKUT SUAMI")
                    TK0
                    @elseif($npwp->status_perkawinan == "KAWIN")
                    K<?= $jumlah ?>
                    @elseif($npwp->status_perkawinan == "CERAI")
                    HB<?= $jumlah ?>
                    @endif
                </td>
                <td>{{ $npwp->nik }}</td>
                <td>{{ $npwp->tempat_lahir }}</td>
                <td>{{ $npwp->tanggal_lahir }}</td>
                <td>{{ $npwp->jenis_kelamin }}</td>
                <td>{{ $npwp->jalan }}</td>
                <td>{{ $npwp->rtrw }}</td>
                <td>{{ $npwp->kelurahan }}</td>
                <td>{{ $npwp->kecamatan }}</td>
                <td>{{ $npwp->kota }}</td>
                <td>{{ $npwp->status_perkawinan }}</td>

                <?php 
                    $istri = explode("_", $npwp->istri);
                ?>
                <td>{{ $istri[0] }}</td>
                <td>{{ $istri[1] }}</td>
                <td>{{ $istri[2] }}</td>
                <td>{{ $istri[3] }}</td>

                <?php 
                    $anak1 = explode("_", $npwp->anak1);
                ?>
                <td>{{ $anak1[0] }}</td>
                <td>{{ $anak1[2] }}</td>
                <td>{{ $anak1[3] }}</td>
                <td>{{ $anak1[4] }}</td>

                <?php 
                    $anak2 = explode("_", $npwp->anak2);
                ?>
                <td>{{ $anak2[0] }}</td>
                <td>{{ $anak2[2] }}</td>
                <td>{{ $anak2[3] }}</td>
                <td>{{ $anak2[4] }}</td>

                <?php 
                    $anak3 = explode("_", $npwp->anak3);
                ?>
                <td>{{ $anak3[0] }}</td>
                <td>{{ $anak3[2] }}</td>
                <td>{{ $anak3[3] }}</td>
                <td>{{ $anak3[4] }}</td>

                <td>{{ $npwp->npwp_kepemilikan }}</td>
                <td>{{ $npwp->npwp_status }}</td>
                <td>{{ $npwp->npwp_nama }}</td>
                <td>{{ $npwp->npwp_nomor }}</td>
                <td>{{ $npwp->npwp_alamat }}</td>
                <td>{{ $npwp->status }}</td>
                <td>{{ $npwp->npwp_change_status }}</td>
                <td>{{ $npwp->npwp_nama_change }}</td>
                <td>{{ $npwp->npwp_nomor_change }}</td>
                <td>{{ $npwp->npwp_alamat_change }}</td>
                
                <?php if ($npwp->npwp_file != null) { ?>

                <?php $photo = json_decode($npwp->npwp_file) ?>

                <td>
                    <?php foreach ($photo as $foto) { ?> 
                    <?php if (file_exists(public_path() .'/tax_files/'.$foto)) { ?>
                    <a href="http://10.109.52.4/mirai/public/tax_files/{{ $foto }}">Link Foto</a>
                    <?php } } ?>
                </td>
                <?php } else { ?>
                <td></td>

                <?php } ?>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>