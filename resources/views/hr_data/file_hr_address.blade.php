<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($resumes))
    <table>
        <thead>
            <tr>
                <th>EMPLOYEE_NAME</th>
                <th>ALAMAT ASAL</th>
                <th>RT</th>
                <th>RW</th>
                <th>DESA/KELURAHAN</th>
                <th>KECAMATAN</th>
                <th>KOTA/KABUPATEN</th>
                <th>ALAMAT DOMISILI</th>
                <th>RT</th>
                <th>RW</th>
                <th>DESA/KELURAHAN</th>
                <th>KECAMATAN</th>
                <th>KOTA/KABUPATEN</th>
                <th>SMA/SMK</th>
                <th>JURUSAN</th>
                <th>AYAH</th>
                <th>IBU</th>
                <th>NAMA-EMERGENCY</th>
                <th>NO TLP-EMERGENCY</th>
                <th>PEKERJAAN-EMERGENCY</th>
                <th>HUBUNGAN-EMERGENCY</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <?php
                $b = $resumes->address;
                $a = explode("/", $b);

                $d = $resumes->current_address;
                $c = explode("/", $d);

                $p = $resumes->sma;
                $z = explode("_", $p);

                $f = $resumes->f_ayah;
                $e = explode("_", $f);

                $h = $resumes->f_ibu;
                $g = explode("_", $h);

                $q = $resumes->emergency1;
                $w = explode("_", $q);
            ?>
            <tr>
                <td>{{ $resumes->name }}</td>
                <td>{{ $a[0] }}</td>
                <td>{{ $a[1] }}</td>
                <td>{{ $a[2] }}</td>
                <td>{{ $a[3] }}</td>
                <td>{{ $a[4] }}</td>
                <td>{{ $a[5] }}</td>
                <td>{{ $c[0] }}</td>
                <td>{{ $c[1] }}</td>
                <td>{{ $c[2] }}</td>
                <td>{{ $c[3] }}</td>
                <td>{{ $c[4] }}</td>
                <td>{{ $c[5] }}</td>
                <td>{{ $z[0] }}</td>
                <td>{{ $z[1] }}</td>
                <td>{{ $e[0] }}</td>
                <td>{{ $g[0] }}</td>
                <td>{{ $w[0] }}</td>
                <td>{{ $w[1] }}</td>
                <td>{{ $w[2] }}</td>
                <td>{{ $w[3] }}</td>
            </tr>
            
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>