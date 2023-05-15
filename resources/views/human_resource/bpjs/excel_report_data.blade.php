<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td>NAMA KARYAWAN</td>
                <td>NO KK</td>
                <td>NO KTP</td>
                <td>NAMA</td>
                <td>TEMPAT LAHIR</td>
                <td>TANGGAL LAHIR</td>
                <td>JENIS KELAMIN</td>
                <td>NO BPJS</td>
                <td>KELAS RAWAT</td>
                <td>KETERANGAN</td>
                <td>HUBUNGAN</td>
                <td>ALAMAT</td>
                <td>KODE POS</td>
                <td>KELURAHAN</td>
                <td>KECAMATAN</td>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <tr>
            <td>{{ $resumes->employee }}</td>
            <td>{{ $resumes->no_kk }}</td>
            <td>{{ $resumes->no_ktp }}</td>
            <td>{{ $resumes->nama }}</td>
            <td>{{ $resumes->tempat_lahir }}</td>
            <td>{{ $resumes->tanggal_lahir }}</td>
            <td>{{ $resumes->jenis_kelamin }}</td>
            <td>{{ $resumes->no_bpjs }}</td>
            <td>{{ $resumes->kelas_rawat }}</td>
            <td>{{ $resumes->remark }}</td>
            <td>{{ $resumes->hubungan }}</td>
            <td>{{ $resumes->alamat }}</td>
            <td>{{ $resumes->kode_post }}</td>
            <td>{{ $resumes->kelurahan }}</td>
            <td>{{ $resumes->kecamatan }}</td>
        </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>