<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td>EMPNO</td>
                <td>EMPNAME</td>
                <td>ALSIMPATIYMPI</td>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <?php
                $b = $resumes->permohonan;
                $a = explode("/", $b);
            ?>
            <tr>
            <td>{{ $resumes->employee }}</td>
            <td>{{ $resumes->name }}</td>
            <td>{{ $a[1] }}</td>
        </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>