<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td>NIK</td>
                <td>NAMA</td>
                <td>JENIS TUNJANGAN</td>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <tr>
            <td>{{ $resumes->employee }}</td>
            <td>{{ $resumes->name }}</td>
            <td>{{ $resumes->permohonan }}</td>
        </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>