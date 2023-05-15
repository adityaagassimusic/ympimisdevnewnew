<!DOCTYPE html>
<html>
<head>
    <style type="text/css">

    </style>
</head>
<body>
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr style="vertical-align: middle; ">
                <th colspan="8" style="text-align: center;">
                    List Data Log Suhu dan Humidity MIRAI
                </th>
            </tr>

            <tr></tr>
        </thead>
        <tbody>

            <tr>
                <th>No</th>
                <th>Lokasi</th>
                <th>Remark</th>
                <th>Value</th>
                <th>Created At</th>
            </tr>

            <?php 
                $num = 1;
            ?>

            @foreach($detail as $temp)
            <tr>
                <td style="vertical-align: middle;text-align: left;width: 5">{{ $num++ }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $temp->location }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $temp->remark }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $temp->value }}</td>
                <td style="vertical-align: middle;text-align: left;width: 40">{{ $temp->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>